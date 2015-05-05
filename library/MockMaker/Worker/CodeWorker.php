<?php
/**
 * CodeWorker
 *
 * This class generates mock code based on the generated MockMakerFileData class.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        4/30/15
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\MockMaker;
use MockMaker\Model\MethodData;
use MockMaker\Model\MockMakerFileData;
use MockMaker\Model\PropertyData;
use MockMaker\Worker\StringFormatterWorker;
use MockMaker\Worker\AbstractCodeWorker;
use MockMaker\Worker\DirectoryWorker;
use MockMaker\Exception\MockMakerErrors;
use MockMaker\Exception\MockMakerException;
use MockMaker\Helper\TestHelper;

class CodeWorker extends AbstractCodeWorker
{

    /**
     * Mock template code file
     *
     * @var string
     */
    protected $mockTemplate;

    /**
     * Mock code to return to the user
     *
     * This is an array to hold any data we need it to. It will be imploded()
     * later on and returned as a string.
     *
     * @var array
     */
    protected $mockCode = [];

    /**
     * Gets the mock code template file
     *
     * @return string
     */
    public function getMockTemplate()
    {
        if (!$this->mockTemplate) {
            return dirname(dirname(__FILE__)) . '/FileTemplates/DefaultMockTemplateStrings.php';
        }

        return $this->mockTemplate;
    }

    /**
     * Gets the mock code
     *
     * @return array
     */
    public function getMockCode()
    {
        return $this->mockCode;
    }

    /**
     * Sets the mock code template file
     *
     * @param string $mockTemplate
     */
    public function setMockTemplate($mockTemplate)
    {
        $this->mockTemplate = $mockTemplate;
    }

    /**
     * Sets the mock code
     *
     * @param   array $mockCode
     */
    public function setMockCode(array $mockCode)
    {
        $this->mockCode = $mockCode;
    }

    /**
     * Adds (single|array of) code to the mock code array
     *
     * @param   string|array $mockCode
     */
    public function addMockCode($mockCode)
    {
        if (is_array($mockCode)) {
            $this->setMockCode(array_merge($this->mockCode, $mockCode));
        } else {
            array_push($this->mockCode, $mockCode);
        }
    }

    /**
     * Generates mocks code from MockMakerFileData objects
     *
     * @param   array $mockMakerFileDataObjects
     * @return  array
     * @throws  MockMakerException
     */
    public function generateCodeFromMockMakerFileDataObjects($mockMakerFileDataObjects)
    {
        if (empty($mockMakerFileDataObjects)) {
            throw new MockMakerException(
                MockMakerErrors::generateMessage(MockMakerErrors::CODE_WORKER_NO_FILE_DATA, ['file' => 'unknown'])
            );
        }
        foreach ($mockMakerFileDataObjects as $mmFileData) {
            $code = '';
            //$code = $this->generateMockCodeFromMockMakerFileDataObject($mmFileData);
            $code .= $this->generateMockUnitTestCodeFromMockMakerFileDataObject($mmFileData);
            $this->addMockCode($code);
            $this->createMockFileIfRequested($mmFileData, $code);
        }

        return implode(PHP_EOL . str_repeat("-", 50) . PHP_EOL, $this->getMockCode());
    }

    /**
     * Generates mock code from a MockMakerFileData object
     *
     * TODO: I need to create a "MockFile" model to put in the FileData
     *
     * @param   MockMakerFileData $mmFileData
     * @return  string
     */
    protected function generateMockCodeFromMockMakerFileDataObject(MockMakerFileData $mmFileData)
    {
        $class = $mmFileData->getClassData();
        $today = new \DateTime('now');
        $date = $today->format('Y-m-d');
        $dataPoints = array(
            'ClassName'            => $class->getClassName(),
            'CreatedDate'          => $date,
            'ClassMockName'        => StringFormatterWorker::vsprintf2($mmFileData->getMockFileNameFormat(),
                array('FileName' => $class->getClassName())),
            'NameSpace'            => $mmFileData->getMockFileNamespace(),
            'UseStatements'        => $this->generateUseStatements($mmFileData),
            'ClassPath'            => $class->getClassNamespace() . "\\" . $class->getClassName(),
            'PropertyDefaults'     => $this->generateArrayOfMandatoryProperties($mmFileData),
            'MockVisibilityArrays' => $this->generateMockVisibilityArrays($mmFileData),
        );

        // throwing PHPUnit errors due to undefined variable: dataPoints
        //$code = include($this->getMockTemplate());

        $templateCode = include($this->getMockTemplate());
        $code = StringFormatterWorker::vsprintf2($templateCode, $dataPoints);

        return $code;
    }

    /**
     * Generates basic unit test code from a MockMakerFileData object
     *
     * TODO: before doing this I need to create a "UnitTestFile" model to put in the FileData
     *
     * @param   MockMakerFileData $mmFileData
     * @return  string
     */
    protected function generateMockUnitTestCodeFromMockMakerFileDataObject(MockMakerFileData $mmFileData)
    {
        $unitTestCode = '';

        TestHelper::dbug($mmFileData, __METHOD__, true);

        return $unitTestCode;
    }

    /**
     * Generates use statements out of namespaces for all classes used by the mocked class
     *
     * To help out with generating mocks, we're gathering the namespaces
     * that are used in typehinting as well as the ones declared at the
     * top of the file.
     *
     * @param   MockMakerFileData $mmFileData
     * @return  string
     */
    protected function generateUseStatements(MockMakerFileData $mmFileData)
    {
        $classData = $mmFileData->getClassData();
        $classUse = $classData->getUseStatements();
        $mockedClassUse = "use {$classData->getClassNamespace()}\\{$classData->getClassName()};";
        array_unshift($classUse, $mockedClassUse);
        $propUse = $this->getUseStatementsFromClassProperties($classData->getProperties());
        $methodUse = $this->getUseStatementsFromClassMethods($classData->getMethods());
        $statements = array_merge($classUse, $propUse, $methodUse);

        return join(PHP_EOL, array_unique($statements));
    }

    /**
     * Extracts use statements from class properties
     *
     * @param   array $classProperties
     * @return  array
     */
    protected function getUseStatementsFromClassProperties($classProperties)
    {
        $statements = [];
        foreach ($classProperties as $visibility => $properties) {
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    if ($property->dataType === 'object') {
                        $nameSpace = ($property->classNamespace) ? $property->classNamespace . "\\" : "";
                        array_push($statements, "use {$nameSpace}{$property->className};");
                    }
                }
            }
        }

        return $statements;
    }

    /**
     * Extracts use statements from class methods
     *
     * @param   array $classMethods
     * @return  array
     */
    protected function getUseStatementsFromClassMethods($classMethods)
    {
        $statements = [];
        foreach ($classMethods as $visibility => $methods) {
            if (!empty($methods)) {
                foreach ($methods as $method) {
                    if (!empty($method->arguments)) {
                        foreach ($method->arguments as $argument) {
                            if ($argument->dataType === 'object') {
                                $nameSpace = ($argument->classNamespace) ? $argument->classNamespace . "\\" : "";
                                array_push($statements, "use {$nameSpace}{$argument->className};");
                            }
                        }
                    }
                }
            }
        }

        return $statements;
    }

    /**
     * Generates the array values for the mandatoryProperties array
     *
     * @param   MockMakerFileData $mmFileData
     * @return  string
     */
    protected function generateArrayOfMandatoryProperties(MockMakerFileData $mmFileData)
    {
        $classProperties = $mmFileData->getClassData()->getProperties();
        $code = '';
        foreach ($classProperties as $visibility => $properties) {
            // constants cannot be overridden, period!
            if (!empty($visibility) && $visibility !== 'constant') {
                $code .= $this->generateMandatoryPropertiesInVisibility($properties);
            }
        }

        return rtrim($code, PHP_EOL);
    }

    /**
     * Gets the mandatory properties code for an array of PropertyData classes
     * in a particular visibility.
     *
     * @param   array $properties
     * @return  string
     */
    protected function generateMandatoryPropertiesInVisibility($properties)
    {
        $code = '';
        $template = "            '%Property%' => %DefaultValue%," . PHP_EOL;
        foreach ($properties as $k => $property) {
            $args = array(
                'Property'     => $property->name,
                'DefaultValue' => $this->generateDefaultValueString($property),
            );
            $code .= StringFormatterWorker::vsprintf2($template, $args);
        }

        return $code;
    }

    /**
     * Generate the default value code for a single property|argument
     *
     * @param    $object ArgumentDetails or PropertyDetails object
     * @return    string
     */
    protected function generateDefaultValueString($object)
    {
        if ($object->dataType === 'object') {
            return "new {$object->className}()";
        }
        if (!empty($object->defaultValue)) {
            return $this->formatValueForArgs($object->dataType, $object->defaultValue);
        }
        if ((isset($object->allowsNull) && $object->allowsNull) || (isset($object->dataType) && $object->dataType)) {
            return 'null';
        }

        return "'_'";
    }

    /**
     * Quick format for argument values.
     *
     * @param    $type    string
     * @param    $arg     string
     * @return    string
     */
    protected function formatValueForArgs($type, $arg)
    {
        if ($type === 'string') {
            return "'{$arg}'";
        }
        if (in_array($type, array('integer', 'double'))) {
            return $arg;
        }

        return "_{$type}_";
    }

    /**
     * Generates the mock visibility arrays
     *
     * @param   MockMakerFileData $mmFileData
     * @return  string
     */
    protected function generateMockVisibilityArrays(MockMakerFileData $mmFileData)
    {
        $code = '';
        $code .= join(PHP_EOL, $this->generatePropertyArrays($mmFileData->getClassData()->getProperties()));

        return $code;
    }

    /**
     * Gets arrays of class properties by visibility
     *
     * Used to define the visibility of each object property.
     * The only one that really causes problems are the 'constant' visibility
     * scope properties. The rest can all be accessed using the same
     * reflection code.
     *
     * @param   array $classProperties
     * @return  array
     */
    protected function generatePropertyArrays($classProperties)
    {
        $propVisStr = [];
        if (!empty($classProperties['constant'])) {
            $visStr = "        \$constant = array(";
            foreach ($classProperties['constant'] as $k => $prop) {
                $visStr .= "'{$prop->name}', ";
            }
            $visStr = rtrim($visStr, ', ') . ');';
        } else {
            $visStr = "        \$constant = [];";
        }
        array_push($propVisStr, $visStr);

        return $propVisStr;
        /*
        $possibleVisibilities = array('constant', 'private', 'protected', 'public', 'static');
        foreach($classProperties as $visibility => $properties) {
            if(!empty($properties)) {
                if(($key = array_search($visibility, $possibleVisibilities)) !== false) {
                    unset($possibleVisibilities[$key]);
                }
                $visStr = "        \${$visibility} = array(";
                foreach($properties as $k => $prop) {
                    $visStr .= "'{$prop->name}', ";
                }
                $visStr = rtrim($visStr, ', ') . ');';
                array_push($propVisStr, $visStr);
            }
        }
        foreach($possibleVisibilities as $visibility) {
            $visStr = "        \${$visibility} = [];";
            array_push($propVisStr, $visStr);
        }
        array_push($propVisStr, '        $priPro = array_merge($private, $protected, $static);');
        */
    }

    /**
     * Saves the generated code to a file
     *
     * @param   MockMakerFileData $mmFileData
     * @param   string            $code
     * @return  bool
     * @throws  \Exception
     */
    protected function createMockFileIfRequested(MockMakerFileData $mmFileData, $code)
    {
        if ($mmFileData->getMockWriteDirectory()) {
            $filePath = $mmFileData->getMockFileSavePath();

            if (!$mmFileData->getOverwriteExistingFiles() && file_exists($filePath)) {
                return false;
            }

            // ensure that the directory we're attempting to write to exists
            $writeDir = substr($filePath, 0, strrpos($filePath, '/'));
            DirectoryWorker::validateWriteDir($writeDir);

            $writeResults = file_put_contents($filePath, $code);
            if (!$writeResults) {
                throw new \Exception("error writing code to file");
            }

            return true;
        }

        return false;
    }
}


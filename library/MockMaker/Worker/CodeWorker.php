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
use MockMaker\Model\MockMakerFileData;
use MockMaker\Model\PropertyData;
use MockMaker\Worker\StringFormatterWorker;
use MockMaker\Worker\AbstractCodeWorker;
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
            return dirname(dirname(__FILE__)) . '/FileTemplates/DefaultMockTemplate.php';
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
     */
    public function generateCodeFromMockMakerFileDataObjects($mockMakerFileDataObjects)
    {
        foreach ($mockMakerFileDataObjects as $mmFileData) {
            //TestHelper::dbug($mmFileData, "file data");
            $code = $this->generateMockCodeFromMockMakerFileDataObject($mmFileData);
            $this->addMockCode($code);
            $this->createMockFileIfRequested($mmFileData, $code);
        }

        return implode(PHP_EOL . str_repeat("-", 50) . PHP_EOL, $this->getMockCode());
    }

    /**
     * Generates mock code from a MockMakerFileData object
     *
     * @param   MockMakerFileData $mmFileData
     * @return  string
     */
    protected function generateMockCodeFromMockMakerFileDataObject(MockMakerFileData $mmFileData)
    {
        /**
         * These data points will have to be generated somehow
         */
        $class = $mmFileData->getClassData();
        $today = new \DateTime('now');
        $date = $today->format('Y-m-d');
        $dataPoints = array(
            'ClassName'                 => $class->getClassName(),
            'CreatedDate'               => $date,
            // TODO: generate a list of all use statements that are needed here
            // to include all children classes in the entity?
            // either do that here or i can do that later on while i'm generating
            // the mandatory properties array
            'NameSpace'                 => $class->getClassNamespace(),
            'UseStatements'             => implode(PHP_EOL, $class->getUseStatements()),
            'ClassMockName'             => StringFormatterWorker::vsprintf2('%ClassName%Mock',
                array('ClassName' => $class->getClassName())),
            'PropertiesAndSettersArray' => $this->generateArrayOfMandatoryProperties($mmFileData),
            'ClassPath'                 => $class->getClassNamespace() . "\\" . $class->getClassName(),
            'SetterCode'                => $this->generateSetterCode($mmFileData),
            'ReflectionCode'            => $this->generateNoSetterCode($mmFileData),
        );

        // this works fine, both with fgc and include
        //$mockCodeTemplate = file_get_contents($this->getMockTemplate());
        //$mockCodeTemplate = include($this->getMockTemplate());
        //$code = StringFormatterWorker::vsprintf2($mockCodeTemplate, $dataPoints);

        // this works too
        $code = include($this->getMockTemplate());

        // since both methods work well for inserting data, i guess it depends on how long each one takes?

        return $code;
    }

    /**
     * Generates the array values for the mandatoryProperties array
     *
     * @param   MockMakerFileData   $mmFileData
     * @return  string
     */
    protected function generateArrayOfMandatoryProperties(MockMakerFileData $mmFileData)
    {
        $classProperties = $mmFileData->getClassData()->getProperties();
        $code = '';
        foreach($classProperties as $visibility => $properties) {
            if(!empty($visibility) ) {
                $code .= $this->generateMandatoryPropertiesInVisibility( $visibility, $properties);
            }
        }

        return rtrim( $code, PHP_EOL );
    }

    /**
     * Gets the mandatory properties code for an array of PropertyData classes
     * in a particular visibility.
     *
     * @param   string  $visibility
     * @param   array   $properties
     * @return  string
     */
    protected function generateMandatoryPropertiesInVisibility($visibility, $properties)
    {
        $code = '';
        $template = "           '%Property%' => array( 'setter' => '%Setter%', 'default' => %DefaultValue% )," . PHP_EOL;
        foreach( $properties as $k => $property) {
            $args = array(
                'Property' => $property->name,
                'Setter' => $property->setter,
                //'DefaultValue' => $this->generateDefaultValueForMandatoryProperties($property),
                'DefaultValue' => $this->generateDefaultValueString($property),
            );
            $code .= StringFormatterWorker::vsprintf2($template, $args);
            //$code .= "      '{$property->name}' => array( 'setter' => '{$property->setter}',";
            //$code .= " 'default' => '{property->defaultValue}' )," . PHP_EOL;
        }

        return $code;
    }

    /**
     * Generate the default value code for a single property|argument
     *
     * @param	$object ArgumentDetails or PropertyDetails object
     * @return	string
     */
    private function generateDefaultValueString($object)
    {
        if ($object->dataType === 'object') {
            // TODO: add class of object to use statements?
            //return $object->className . " \$" . lcfirst($object->name);
            return "new {$object->className}()";
        }
        if (!empty($object->defaultValue)) {
            return $this->formatValueForArgs($object->dataType, $object->defaultValue);
        }
        if ((isset($object->allowsNull) && $object->allowsNull) || (isset($object->dataType) && $object->dataType)) {
            return 'NULL';
        }

        return "'_'";
    }

    /**
     * Quick format for argument values.
     *
     * @param	$type	string
     * @param	$arg	string
     * @return	string
     */
    protected function formatValueForArgs($type, $arg)
    {
        if ($type === 'string') {
            return "'{$arg}'";
        }
        if (in_array($type, array( 'integer', 'double' ))) {
            return $arg;
        }

        return "_{$type}_";
    }

    /**
     * Generates the code properties with setters
     *
     * @param   MockMakerFileData   $mmFileData
     * @return  string
     */
    protected function generateSetterCode(MockMakerFileData $mmFileData)
    {
        return '        // the setter method code';
    }

    /**
     * Generates the code properties that do not have setters
     *
     * @param   MockMakerFileData   $mmFileData
     * @return  string
     */
    protected function generateNoSetterCode(MockMakerFileData $mmFileData)
    {
        return '        // non-setter method code via reflection';
    }

    /**
     * @param MockMakerFileData $mmFileData
     * @param string            $code
     */
    protected function createMockFileIfRequested(MockMakerFileData $mmFileData, $code)
    {
        if ($mmFileData->getMockWriteDirectory()) {
            $fileName = $mmFileData->getMockWriteDirectory() . $mmFileData->getClassData()->getClassName() . 'Mock.php';
            TestHelper::dbug($fileName, "file name to save under", true);
            die();
        }
    }
}
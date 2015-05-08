<?php

/**
 * DefaultMockDataPointWorker
 *
 * Default DataPointWorker class for MockMaker
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/8/15
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\DataContainer;
use MockMaker\Model\MockData;

class DefaultMockDataPointWorker extends AbstractDataPointWorker
{

    public function __construct()
    {
        $this->template = dirname(dirname(__FILE__)) . '/FileTemplates/DefaultMockTemplate.php';
        $this->registerAdditionalProcess('saveMockFile');
    }

    /**
     * Generates the data points required to populate the template file
     *
     * @param   DataContainer $dataContainer
     * @return  array
     */
    public function generateDataPoints(DataContainer $dataContainer)
    {
        $mock = $dataContainer->getMockData();
        $entity = $dataContainer->getEntityData();
        $today = new \DateTime('now');
        $date = $today->format('Y-m-d');
        $dataPoints = array(
            'EntityClassName'      => $entity->getClassName(),
            'CreatedDate'          => $date,
            'ClassName'            => $mock->getClassName(),
            'NameSpace'            => $mock->getClassNamespace(),
            'UseStatements'        => $this->generateUseStatements($mock, $entity),
            'ClassPath'            => $entity->getClassNamespace() . '\\' . $entity->getClassName(),
            'PropertyDefaults'     => $this->generateArrayOfMandatoryProperties($mock),
            'PropertyConstantsArr' => $this->getPropertyConstants($mock),
        );

        return $dataPoints;
    }

    /**
     * Processes the generated code as appropriate
     *
     * @param   DataContainer $dataContainer
     * @param   string        $code
     * @return  bool
     */
    public function processCode(DataContainer $dataContainer, $code)
    {
        if (!$dataContainer->getConfigData()->getMockWriteDir()) {
            return false;
        }
        $filePath = $dataContainer->getMockData()->getFileData()->getFullFilePath();
        if (!$dataContainer->getConfigData()->getOverwriteMockFiles() && file_exists($filePath)) {
            return false;
        }

        return $this->writeFile($filePath, $code);
    }

    /**
     * Generates the array values for the mandatoryProperties array
     *
     * @param   MockData $mockData MockData object
     * @return  string
     */
    private function generateArrayOfMandatoryProperties(MockData $mockData)
    {
        $classProperties = $mockData->getProperties();
        $code = '';
        foreach ($classProperties as $visibility => $properties) {
            // constants cannot be overridden, period!
            if (!empty($visibility) && $visibility !== 'constant') {
                $code .= $this->getMandatoryPropsCode($properties);
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
    private function getMandatoryPropsCode($properties)
    {
        $code = '';
        $template = "            '%Property%' => %DefaultValue%," . PHP_EOL;
        foreach ($properties as $property) {
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
     * @param   object $object ArgumentDetails or PropertyDetails object
     * @return  string
     */
    private function generateDefaultValueString($object)
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
    private function formatValueForArgs($type, $arg)
    {
        if ($type === 'string') {
            return "'{$arg}'";
        }
        if (in_array($type, array('integer', 'double'))) {
            return $arg;
        }
        if ($type === 'array') {
            return 'array()';
        }
        if ($type === 'boolean') {
            return "(bool){$arg}";
        }

        return "_{$type}_";
    }

    /**
     * Generates the mock visibility arrays
     *
     * @param   MockData $mockData MockData object
     * @return  string
     */
    private function getPropertyConstants(MockData $mockData)
    {
        $visStr = "        \$constant = [];";
        $properties = $mockData->getProperties();
        if (!empty($properties['constant'])) {
            $visStr = "        \$constant = array(";
            foreach ($properties['constant'] as $prop) {
                $visStr .= "'{$prop->name}', ";
            }
            $visStr = rtrim($visStr, ', ') . ');';
        }

        return $visStr;
    }
}
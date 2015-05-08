<?php

/**
 * DefaultUnitTestDataPointWorker
 *
 * <Class Description>
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/8/15
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\DataContainer;
use MockMaker\Model\MockData;

class DefaultUnitTestDataPointWorker extends AbstractDataPointWorker
{

    public function __construct()
    {
        $this->template = dirname(dirname(__FILE__)) . '/FileTemplates/DefaultMockUnitTestTemplate.php';
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
        $args = $this->getMockArgs($mock);
        $dataPoints = array(
            'EntityClassName' => $entity->getClassName(),
            'CreatedDate'     => $date,
            'ClassName'       => $mock->getClassName() . 'Test',
            'NameSpace'       => $mock->getUtClassNamespace(),
            'InstanceOf'      => "{$entity->getClassNamespace()}\\{$entity->getClassName()}",
            'MockClassName'   => $mock->getClassName(),
            'UseStatements'   => "use {$mock->getClassNamespace()}\\{$mock->getClassName()};",
            'MockArgs'        => $this->buildGetMockArgs($args),
            'MockArgAsserts'  => $this->buildGetMockAsserts($args),
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
        if (!$dataContainer->getConfigData()->getUnitTestWriteDir()) {
            return false;
        }
        $filePath = $dataContainer->getMockData()->getUtFileData()->getFullFilePath();
        if (!$dataContainer->getConfigData()->getOverwriteUnitTestFiles() && file_exists($filePath)) {
            return false;
        }

        return $this->writeFile($filePath, $code);
    }

    /**
     * Gets an array of arguments to use in the mock tests
     *
     * @param   MockData $mockData
     * @return  array
     */
    private function getMockArgs(MockData $mockData)
    {
        $args = [];
        $default = (object)array(
            'visibility' => 'private',
            'property'   => 'id',
            'value'      => 1,
        );
        // try to get one from public/protected/private
        $props = $mockData->getProperties();
        foreach (array('public', 'private', 'protected') as $vis) {
            if (!empty($props[$vis])) {
                if ($arg = $this->getRandomProperty($props[$vis])) {
                    $args[] = $arg;
                }
            }
        }

        return (empty($args)) ? array($default) : $args;
    }

    /**
     * Gets a property name and random value for use in tests
     *
     * @param   array $props Array of PropertyData objects
     * @return  object
     */
    private function getRandomProperty($props)
    {
        $sub = [];
        foreach ($props as $prop) {
            //if (!in_array($prop->dataType, array('object', 'NULL'))) {
            if ($prop->dataType !== 'object') {
                $sub[] = $prop;
            }
        }
        if (empty($sub)) {
            return false;
        }
        $prop = $sub[array_rand($sub)];

        return (object)array(
            'visibility' => $prop->visibility,
            'property'   => $prop->name,
            'value'      => $this->getRandomValue($prop->dataType),
        );
    }

    /**
     * Gets a random value for the various datatypes
     *
     * @param   string $type
     * @return  array|bool|int|string
     */
    private function getRandomValue($type)
    {
        switch (true) {
            case ($type === 'integer'):
                return rand(1, 1000);
                break;
            case ($type === 'boolean'):
                return '(bool)' . (rand(1, 10) <= 5) ? 1 : 0;
                break;
            case ($type === 'string'):
                return "'" . uniqid() . "'";
                break;
            case ($type === 'array'):
                return "array( 'testData' => '" . uniqid() . "' )";
                break;
            default:
                return "'" . uniqid() . "'";
                break;
        }
    }

    /**
     * Builds the argument strings for the unit test
     *
     * @param   array $args Random args from class
     * @return  string
     */
    private function buildGetMockArgs($args)
    {
        $str = '';
        foreach ($args as $arg) {
            $str .= "           '{$arg->property}' => {$arg->value}," . PHP_EOL;
        }

        return rtrim($str, PHP_EOL);
    }

    /**
     * Builds the assertion strings for the unit test
     *
     * @param   array $args Random args from class
     * @return  string
     */
    private function buildGetMockAsserts($args)
    {
        $str = '';
        foreach ($args as $arg) {
            $expected = "\$args['{$arg->property}']";
            $actual = "\$actual->{$arg->property}";
            if ($arg->visibility !== 'public') {
                $actual = "\$actual->get" . ucfirst($arg->property) . "()";
            }
            $str .= "        \$this->assertEquals({$expected}, {$actual});" . PHP_EOL;
        }

        return rtrim($str, PHP_EOL);
    }
}
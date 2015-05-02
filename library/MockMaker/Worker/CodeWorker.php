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

use MockMaker\Model\MockMakerFileData;
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
    private $mockTemplate;

    /**
     * Mock code to return to the user
     *
     * This is an array to hold any data we need it to. It will be imploded()
     * later on and returned as a string.
     *
     * @var array
     */
    private $mockCode = [];

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
            TestHelper::dbug($mmFileData, "file data");
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
    private function generateMockCodeFromMockMakerFileDataObject(MockMakerFileData $mmFileData)
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
            'NameSpace'                 => $class->getClassNamespace(),
            'UseStatements'             => implode(PHP_EOL, $class->getUseStatements()),
            'ClassMockName'             => StringFormatterWorker::vsprintf2('%ClassName%Mock',
                array('ClassName' => $class->getClassName())),
            'PropertiesAndSettersArray' => '        // default required properties to instantiate the class',
            'ClassPath'                 => $class->getClassNamespace() . "\\" . $class->getClassName(),
            'SetterCode'                => '        // the setter method code',
            'ReflectionCode'            => '        // non-setter method code via reflection',
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
     * @param MockMakerFileData $mmFileData
     * @param string            $code
     */
    private function createMockFileIfRequested(MockMakerFileData $mmFileData, $code)
    {
        if ($mmFileData->getMockWriteDirectory()) {
            $fileName = $mmFileData->getMockWriteDirectory() . $mmFileData->getClassData()->getClassName() . 'Mock.php';
            TestHelper::dbug($fileName, "file name to save under", true);
            die();
        }
    }
}
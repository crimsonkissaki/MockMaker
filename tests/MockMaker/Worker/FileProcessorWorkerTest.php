<?php
/**
 * FileProcessorWorkerTest
 *
 * @package     MockMaker
 * @author		Evan Johnson
 * @created	    5/5/15
 * @version     1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\FileProcessorWorker;
use MockMaker\Model\DataContainer;
use MockMaker\Model\ConfigData;
use MockMaker\Exception\MockMakerException;
use MockMaker\TestHelper\TestHelper;

class FileProcessorWorkerTest extends \PHPUnit_Framework_TestCase
{

    /* @var $worker FileProcessorWorker */
    public $worker;
    /* @var $config ConfigData */
    public $config;
    public $rootDir;

    public function setUp()
    {
        $this->config = new ConfigData();
        $this->worker = new FileProcessorWorker($this->config);
        $this->rootDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    }

    public function test_addFileData_addsSingleElementToArray()
    {
        $dc = new DataContainer();
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'addFileData');
        $method->invoke($this->worker, $dc);
        $actual = TestHelper::getNonPublicValue($this->worker, 'fileData');
        $this->assertEquals(1, count($actual));
    }

    public function test_addFileData_addsArrayOfElements()
    {
        $args = array(
            new DataContainer(),
            new DataContainer(),
            new DataContainer(),
        );
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'addFileData');
        $method->invoke($this->worker, $args);
        $actual = TestHelper::getNonPublicValue($this->worker, 'fileData');
        $this->assertEquals(3, count($actual));
    }

    public function test_processFiles_returnsString()
    {
        $file = $this->rootDir . 'tests/MockMaker/Entities/TestEntity.php';
        $config = TestHelper::getNonPublicValue($this->worker, 'config');
        $config->addFilesToMock($file);
        $actual = $this->worker->processFiles();
        $this->assertTrue(is_string($actual));
    }

    public function test_processFile_returnsDataContainerObject()
    {
        $file = $this->rootDir . 'tests/MockMaker/Entities/SimpleEntity.php';
        $config = TestHelper::getNonPublicValue($this->worker, 'config');
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'processFile');
        $actual = $method->invoke($this->worker, $file, $config);
        $this->assertInstanceOf('MockMaker\Model\DataContainer', $actual);
    }

}
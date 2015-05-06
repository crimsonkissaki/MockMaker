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
use MockMaker\Model\MockMakerFileData;
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
        $fd = new MockMakerFileData();
        $this->worker->addFileData($fd);
        $this->assertEquals(1, count($this->worker->getFileData()));
    }

    public function test_addFileData_addsArrayOfElements()
    {
        $args = array(
            new MockMakerFileData(),
            new MockMakerFileData(),
            new MockMakerFileData(),
        );
        $this->worker->addFileData($args);
        $this->assertEquals(3, count($this->worker->getFileData()));
    }

    public function test_processFiles_returnsArrayOfMockMakerFileDataObject()
    {
        $file = $this->rootDir . 'tests/MockMaker/Entities/TestEntity.php';
        $this->worker->getConfig()->addFilesToMock($file);
        $actual = $this->worker->processFiles();
        $this->assertTrue(is_array($actual));
        $this->assertInstanceOf('MockMaker\Model\MockMakerFileData', $actual[0]);
    }

    public function test_processFile_returnsSingleMockMakerFileDataObject()
    {
        $file = $this->rootDir . 'tests/MockMaker/Entities/TestEntity.php';
        $this->worker->getConfig()->addFilesToMock($file);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'processFile');
        $actual = $method->invoke($this->worker, $file, $this->worker->getConfig());
        $this->assertInstanceOf('MockMaker\Model\MockMakerFileData', $actual);
    }

    public function processFileExceptionProvider()
    {
        return array(
            array( $this->rootDir . 'tests/MockMaker/Entities/AbstractEntity.php' ),
            array( $this->rootDir . 'tests/MockMaker/Entities/EntityInterface.php' ),
        );
    }

    /**
     * @dataProvider processFileExceptionProvider
     */
    public function test_processFile_throwsExceptionIfAbstractClass($file)
    {
        $this->setExpectedException('MockMaker\Exception\MockMakerException');
        $this->worker->getConfig()->addFilesToMock($file);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'processFile');
        $actual = $method->invoke($this->worker, $file, $this->worker->getConfig());
    }

    public function test_generateFileDataObject_returnsSingleMockMakerFileDataObject()
    {
        $file = $this->rootDir . 'tests/MockMaker/Entities/TestEntity.php';
        $this->worker->getConfig()->addFilesToMock($file);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'generateFileDataObject');
        $actual = $method->invoke($this->worker, $file, $this->worker->getConfig());
        $this->assertInstanceOf('MockMaker\Model\MockMakerFileData', $actual);
    }


}
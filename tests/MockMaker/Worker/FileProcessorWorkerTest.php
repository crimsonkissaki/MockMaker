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
use MockMaker\TestHelper\TestHelper;

class FileProcessorWorkerTest extends \PHPUnit_Framework_TestCase
{

    /* @var $worker FileProcessorWorker */
    public $worker;

    public $config;

    public function setUp()
    {
        $this->config = new ConfigData();
        $this->worker = new FileProcessorWorker($this->config);
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

}
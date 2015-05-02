<?php

/**
 * 	MockMakerFileDataWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\MockMakerFileDataWorker;
use MockMaker\Model\ConfigData;

class MockMakerFileDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker MockMakerFileDataWorker
    public $worker;
    // @var $config ConfigData
    public $config;
    public $file = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->worker = new MockMakerFileDataWorker();
        $this->config = new ConfigData();
    }

    public function test_generateNewObject_returnsCorrectFullFilePath()
    {
        $actual = $this->worker->generateNewObject($this->file, $this->config);
        $this->assertEquals($this->file, $actual->getSourceFileFullPath());
    }

    public function test_generateNewObject_returnsCorrectFileName()
    {
        $actual = $this->worker->generateNewObject($this->file, $this->config);
        $this->assertEquals('SimpleEntity.php', $actual->getSourceFileName());
    }

}

<?php

/**
 * 	FileDataWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\FileDataWorker;
use MockMaker\Model\ConfigData;

class FileDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker FileDataWorker
    public $worker;
    // @var $config ConfigData
    public $config;
    public $file = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->worker = new FileDataWorker();
        $this->config = new ConfigData();
    }

    public function test_generateNewObject_returnsCorrectFullFilePath()
    {
        $actual = $this->worker->generateNewObject($this->file, $this->config);
        $this->assertEquals($this->file, $actual->getFullFilePath());
    }

    public function test_generateNewObject_returnsCorrectFileName()
    {
        $actual = $this->worker->generateNewObject($this->file, $this->config);
        $this->assertEquals('SimpleEntity.php', $actual->getFileName());
    }

}

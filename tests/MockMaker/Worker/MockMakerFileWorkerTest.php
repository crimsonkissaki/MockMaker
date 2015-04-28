<?php

/**
 * 	MockMakerFileWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\MockMakerFileWorker;

class MockMakerFileWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker MockMakerFileWorker
    public $worker;
    public $file = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->worker = new MockMakerFileWorker();
    }

    public function test_generateNewObj_returnsCorrectFullFilePath()
    {
        $actual = $this->worker->generateNewObj($this->file);
        $this->assertEquals($this->file, $actual->getFullFilePath());
    }

    public function test_generateNewObj_returnsCorrectFileName()
    {
        $actual = $this->worker->generateNewObj($this->file);
        $this->assertEquals('SimpleEntity.php', $actual->getFileName());
    }

    public function test_generateNewObj_returnsCorrectUseStatements()
    {
        $expected = array(
            'MockMaker\Entities\TestEntity',
            'MockMaker\Entities\PropertyWorkerEntity',
        );
        $actual = $this->worker->generateNewObj($this->file);
        $this->assertEquals($expected, $actual->getUseStatements());
    }

}

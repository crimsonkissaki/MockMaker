<?php

/**
 * 	MockMakerClassWorkerTest
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\MockMakerClassWorker;
use MockMaker\Worker\MockMakerFileWorker;
use MockMaker\Model\MockMakerFile;

class MockMakerClassWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker MockMakerClassWorker
    public $worker;
    // @var $fileObj MockMakerFile
    public $fileObj;
    public $file = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->worker = new MockMakerClassWorker();
        $fileWorker = new MockMakerFileWorker();
        $this->fileObj = $fileWorker->generateNewObject($this->file);
    }

    public function test_generateNewObject_returnsCorrectUseStatements()
    {
        $expected = array(
            'MockMaker\Entities\TestEntity',
            'MockMaker\Entities\PropertyWorkerEntity',
        );
        $actual = $this->worker->generateNewObject($this->fileObj);
        $this->assertEquals($expected, $actual->getUseStatements());
    }

}

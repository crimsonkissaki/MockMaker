<?php

/**
 * 	FileDataWorkerTest
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 29, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\FileDataWorker;
use MockMaker\Helper\TestHelper;

class FileDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    public $worker;

    public function setUp()
    {
        $this->worker = new FileDataWorker();
    }

    public function test_mm()
    {
        $this->assertTrue(false);
    }

}

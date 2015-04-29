<?php

/**
 * 	ClassDataWorkerTest
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 29, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\ClassDataWorker;
use MockMaker\Helper\TestHelper;

class ClassDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    public $worker;

    public function setUp()
    {
        $this->worker = new ClassDataWorker();
    }

    public function test_mm()
    {
        $this->assertTrue(false);
    }

}

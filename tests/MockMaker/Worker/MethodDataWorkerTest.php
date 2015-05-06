<?php

/**
 * 	MethodDataWorkerTest
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 29, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\MethodDataWorker;
use MockMaker\TestHelper\TestHelper;

class MethodDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    public $worker;

    public function setUp()
    {
        $this->worker = new MethodDataWorker();
    }

    public function _test_mm()
    {
        $this->assertTrue(false);
    }

}

<?php

/**
 * 	SimpleEntity
 *
 * 	A very simple entity used for testing.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 19, 2015
 * 	@version	1.0
 */

namespace MockMaker\Entities;

// these aren't actually used, they're just here for testing
use MockMaker\Entities\TestEntity;
use MockMaker\Entities\PropertyWorkerEntity;

class SimpleEntity
{

    public $publicProperty;
    private $privateProperty;
    protected $protectedProperty;

    public function publicFunction()
    {
        return true;
    }

    private function privateFunction()
    {
        return true;
    }

    protected function protectedFunction()
    {
        return true;
    }

}

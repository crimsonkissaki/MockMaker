<?php

/**
 * 	SimpleSubEntityUnderscore
 *
 * 	A very simple entity used for testing sub-folders & recursion reading.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 19, 2015
 * 	@version	1.0
 */

namespace MockMaker\Entities\SubEntities;

class SimpleSubEntityUnderscore
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

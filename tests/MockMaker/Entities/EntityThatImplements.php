<?php

/**
 * 	EntityThatImplements
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Entities;

use MockMaker\Entities\EntityInterface;

class EntityThatImplements implements EntityInterface
{

    public $someValue;
    public $publicProperty;

    public function getSomeValue()
    {
        return $this->someValue;
    }

    public function setSomeValue($someValue)
    {
        $this->someValue = $someValue;
    }

    public function performSomeOperation($argument1, $argument2)
    {
        $this->publicProperty = $argument1 . "::" . $argument2;
    }

}

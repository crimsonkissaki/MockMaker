<?php

/**
 * 	AbstractEntity
 *
 *  Test abstract entity class.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Entity;

abstract class AbstractEntity
{

    public $publicProperty;
    protected $protectedProperty;

    public function setPublicProperty($publicProperty)
    {
        $this->publicProperty = $publicProperty;
    }

    public function getPublicProperty()
    {
        return $this->publicProperty;
    }

    abstract protected function setProtectedProperty();
}

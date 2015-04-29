<?php

/**
 * 	EntityInterface
 *
 *  Test entity interface.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Entities;

interface EntityInterface
{

    public function getSomeValue();

    public function setSomeValue($someValue);

    public function performSomeOperation($argument1, $argument2);
}

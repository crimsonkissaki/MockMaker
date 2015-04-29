<?php

/**
 * 	EntityThatExtends
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Entities;

use MockMaker\Entities\AbstractEntity;

class EntityThatExtends extends AbstractEntity
{

    protected function setProtectedProperty()
    {
        $this->protectedProperty = __METHOD__;
    }

}

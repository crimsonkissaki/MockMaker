<?php

/**
 * 	MethodData
 *
 *  Class that holds all information for a method.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

class MethodData
{

    /**
     * Name of the method.
     *
     * @var string
     */
    public $name;

    /**
     * Visibility of the method: public/public/protected/etc.
     *
     * @var string
     */
    public $visibility;

    /**
     * Is the method a setter.
     *
     * @var bool
     */
    public $isSetter;

    /**
     * Array of ArgumentData objects.
     *
     * @var array
     */
    public $arguments = [ ];

}

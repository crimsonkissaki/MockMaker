<?php

/**
 * 	MockMakerMethod
 *
 *  Class that holds all information for a method.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

class MockMakerMethod
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
     * Array of MockMakerArgument objects.
     *
     * @var array
     */
    public $arguments = [ ];

}

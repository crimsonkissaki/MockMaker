<?php

/**
 * MethodData
 *
 * Class that holds all information for a method
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

class MethodData
{

    /**
     * Name of the method
     *
     * @var string
     */
    public $name;

    /**
     * Visibility of the method
     *
     * Values are public, public, protected.
     *
     * @var string
     */
    public $visibility;

    /**
     * Is the method a setter
     *
     * @var bool
     */
    public $isSetter;

    /**
     * Array of ArgumentData objects
     *
     * @var array
     */
    public $arguments = [];

}

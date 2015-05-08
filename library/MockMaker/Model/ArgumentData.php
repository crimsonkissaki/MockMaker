<?php

/**
 * ArgumentData
 *
 * Class that holds method argument details
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

class ArgumentData
{

    /**
     * Name of the argument
     *
     * @var string
     */
    public $name;

    /**
     * If the argument is passed by reference
     *
     * @var bool
     */
    public $passedByReference = false;

    /**
     * If the argument allows null value
     * (Either by default value or no typehint restrictions)
     *
     * @var bool
     */
    public $allowsNull = false;

    /**
     * Default argument value, if any
     *
     * @var mixed
     */
    public $defaultValue;

    /**
     * If the argument is required
     *
     * @var bool
     */
    public $isRequired = true;

    /**
     * Data type of the argument
     *
     * @var string
     */
    public $dataType;

    /**
     * Class name of object argument types
     *
     * @var string
     */
    public $className;

    /**
     * Class namespace of object argument types
     *
     * @var string
     */
    public $classNamespace;

}

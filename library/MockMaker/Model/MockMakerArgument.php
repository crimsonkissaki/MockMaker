<?php

/**
 * 	MockMakerArgument
 *
 *  Class that holds method argument details.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

class MockMakerArgument
{

    /**
     * Name of the argument.
     *
     * @var string
     */
    public $name;

    /**
     * Is the argument passed by reference?
     *
     * @var bool
     */
    public $passedByReference = false;

    /**
     * Does the argument allow a null value?
     * (Either by default value or no typehint restrictions)
     *
     * @var type
     */
    public $allowsNull = false;

    /**
     * Default argument value, if any.
     *
     * @var mixed
     */
    public $defaultValue;

    /**
     * Is the argument required?
     *
     * @var bool
     */
    public $isRequired = true;

    /**
     * Data type of the argument.
     *
     * @var string
     */
    public $dataType;

    /**
     * Argument typehint, if any.
     *
     * @var type
     */
    public $typeHint;

    /**
     * Object name of object argument types.
     *
     * @var string
     */
    public $className;

    /**
     * Object namespace of object argument types.
     *
     * @var string
     */
    public $classNamespace;

}

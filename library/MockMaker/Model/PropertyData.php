<?php

/**
 * PropertyData
 *
 * Class that holds all information for a class property
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

class PropertyData
{

    /**
     * Name of the property
     *
     * @var string
     */
    public $name;

    /**
     * Visibility of the property
     *
     * Values are public, public, protected.
     *
     * @var string
     */
    public $visibility;

    /**
     * Is the property static
     *
     * @var bool
     */
    public $isStatic;

    /**
     * Setter method if any
     *
     * @var string
     */
    public $setter;

    /**
     * Default property value, if any
     *
     * @var mixed
     */
    public $defaultValue;

    /**
     * Data type of the property
     *
     * @var string
     */
    public $dataType;

    /**
     * Class name of object property types
     *
     * @var string
     */
    public $className;

    /**
     * Class namespace of object property types
     *
     * @var string
     */
    public $classNamespace;

    /**
     * Any comments that need to be displayed above the property in the mock
     *
     * @var string
     */
    public $comments;

    /**
     * ORM data obtained from Entity class
     *
     * @var OrmData
     */
    public $ormData;

}

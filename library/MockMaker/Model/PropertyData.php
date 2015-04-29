<?php

/**
 * 	PropertyData
 *
 *  Class that holds all information for a property.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

class PropertyData
{

    /**
     * Name of the property.
     *
     * @var string
     */
    public $name;

    /**
     * Visibility of the property: public/public/protected
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
     * Default property value, if any.
     *
     * @var mixed
     */
    public $defaultValue;

    /**
     * Data type of the property.
     *
     * @var string
     */
    public $dataType;

    /**
     * Object name of object property types.
     *
     * @var string
     */
    public $className;

    /**
     * Object namespace of object property types.
     *
     * @var string
     */
    public $classNamespace;

}

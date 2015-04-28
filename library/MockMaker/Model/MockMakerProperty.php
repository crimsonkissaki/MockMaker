<?php

/**
 * 	MockMakerProperty
 *
 *  Class that holds all information for a property.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

class MockMakerProperty
{

    /**
     * Name of the property.
     *
     * @var string
     */
    private $name;

    /**
     * Visibility of the property: public/private/protected
     *
     * @var string
     */
    private $visibility;

    /**
     * Data type of the property.
     *
     * @var string
     */
    private $type;

    /**
     * Default property value, if any.
     *
     * @var mixed
     */
    private $defaultValue;

    /**
     * If the property is a class, this is the fully qualified class
     * name of the property type.
     *
     * @var string
     */
    private $fullClassName;

    /**
     * If the property is a class, this is the class
     * name of the property type.
     *
     * @var string
     */
    private $className;

}

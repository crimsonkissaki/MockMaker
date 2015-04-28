<?php

/**
 * 	MockMakerClass
 *
 *  Class model that holds all class-specific information.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

class MockMakerClass
{

    /**
     * Class name
     *
     * @var string
     */
    private $className;

    /**
     * Is this a normal/abstract/interface class?
     *
     * @var string
     */
    private $classType = 'normal';

    /**
     * Does the class have a constructor.
     *
     * @var bool
     */
    private $constructor = false;

    /**
     * Classes that are implemented by the target class.
     *
     * @var array
     */
    private $implements = [ ];

    /**
     * Classes that are extended by the target class.
     *
     * @var array
     */
    private $extends = [ ];

    /**
     * Array of MockMakerProperty objects.
     *
     * @var array
     */
    private $properties = [ ];

    /**
     * Array of MockMakerMethod objects.
     *
     * @var array
     */
    private $methods = [ ];

}

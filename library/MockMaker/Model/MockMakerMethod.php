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
    private $name;

    /**
     * Visibility of the method: public/private/protected
     *
     * @var string
     */
    private $visibility;

    /**
     * Array of MockMakerArgument objects.
     *
     * @var array
     */
    private $arguments = [ ];

}

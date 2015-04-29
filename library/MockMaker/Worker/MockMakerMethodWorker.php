<?php

/**
 * 	MockMakerMethodWorker
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerMethod;
use MockMaker\Worker\MockMakerArgumentWorker;

class MockMakerMethodWorker
{

    /**
     * Class that handles generation of MockMakerArgument objects.
     *
     * @var MockMakerArgumentWorker
     */
    private $argumentWorker;

    public function __construct()
    {
        $this->argumentWorker = new MockMakerArgumentWorker();
    }

    /**
     * Generate an array of MockMakerMethod objects.
     *
     * @param   $class  \ReflectionClass
     * @return  array
     */
    public function generateMethodObjects(\ReflectionClass $class)
    {
        $classMethods = $this->getClassMethodsByVisibility($class);
        $classMethodDetails = $this->getAllClassMethodDetails($classMethods);

        return $classMethodDetails;
    }

    private function getClassMethodsByVisibility(\ReflectionClass $class)
    {
        $classMethods = [ ];
        $classMethods['abstract'] = $class->getMethods(\ReflectionMethod::IS_ABSTRACT);
        $classMethods['final'] = $class->getMethods(\ReflectionMethod::IS_FINAL);
        $classMethods['public'] = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $classMethods['protected'] = $class->getMethods(\ReflectionMethod::IS_PROTECTED);
        $classMethods['private'] = $class->getMethods(\ReflectionMethod::IS_PRIVATE);
        $classMethods['static'] = $class->getMethods(\ReflectionMethod::IS_STATIC);

        return $classMethods;
    }

    /**
     * Get all of the class's method's details.
     *
     * @param	$classMethods	array	Array of \ReflectionMethod objects.
     * @return	array
     */
    private function getAllClassMethodDetails($classMethods)
    {
        $classMethodDetails = [ ];
        foreach ($classMethods as $visibility => $methods) {
            if (!empty($methods)) {
                $classMethodDetails[$visibility] = $this->getMethodDetailsByVisibility($visibility, $methods);
            }
        }

        return $classMethodDetails;
    }

    /**
     * Get the method details in a particular visibility
     *
     * @param	$visibility     string      Public/private/protected/etc
     * @param	$methods	    array       Array of \ReflectionMethod objects
     * @return	array
     */
    private function getMethodDetailsByVisibility($visibility, $methods)
    {
        $details = [ ];
        foreach ($methods as $key => $value) {
            array_push($details, $this->getMethodDetails($visibility, $value));
        }

        return $details;
    }

    /**
     * Get the details for a method.
     *
     * @param	$visibility     string
     * @param	$method		    \ReflectionMethod
     * @return	MockMakerMethod
     */
    private function getMethodDetails($visibility, \ReflectionMethod $method)
    {
        $details = new MockMakerMethod();
        $details->name = $method->getName();
        $details->visibility = $visibility;
        $details->isSetter = (preg_match('/^set/', $method->getName()) === 1) ? true : false;
        $details->arguments = $this->argumentWorker->generateArgumentObjects($method);

        return $details;
    }

}

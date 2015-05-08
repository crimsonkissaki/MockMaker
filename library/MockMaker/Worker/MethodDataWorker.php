<?php

/**
 * MethodDataWorker
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 28, 2015
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MethodData;

class MethodDataWorker
{

    /**
     * Class that handles generation of ArgumentData objects
     *
     * @var ArgumentDataWorker
     */
    private $argumentWorker;

    public function __construct()
    {
        $this->argumentWorker = new ArgumentDataWorker();
    }

    /**
     * Generate an array of MethodData objects
     *
     * @param   array $classMethods Array of \ReflectionMethod objects
     * @return  array
     */
    public function generateMethodObjects(array $classMethods)
    {
        $classMethodDetails = [];
        foreach ($classMethods as $visibility => $methods) {
            if (!empty($methods)) {
                $classMethodDetails[$visibility] = $this->getMethodDetailsByVisibility($visibility, $methods);
            }
        }

        return $classMethodDetails;
    }

    /**
     * Gets the class's methods through a reflection class
     *
     * @param   \ReflectionClass $class
     * @return  array
     */
    public function getClassMethods(\ReflectionClass $class)
    {
        $classMethods = [];
        $classMethods['abstract'] = $class->getMethods(\ReflectionMethod::IS_ABSTRACT);
        $classMethods['final'] = $class->getMethods(\ReflectionMethod::IS_FINAL);
        $classMethods['public'] = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $classMethods['protected'] = $class->getMethods(\ReflectionMethod::IS_PROTECTED);
        $classMethods['private'] = $class->getMethods(\ReflectionMethod::IS_PRIVATE);
        $classMethods['static'] = $class->getMethods(\ReflectionMethod::IS_STATIC);

        return $classMethods;
    }

    /**
     * Gets the method details in a particular visibility
     *
     * @param    string $visibility Method visibility
     * @param    array  $methods    Array of \ReflectionMethod objects
     * @return    array
     */
    private function getMethodDetailsByVisibility($visibility, $methods)
    {
        $details = [];
        foreach ($methods as $value) {
            array_push($details, $this->getMethodDetails($visibility, $value));
        }

        return $details;
    }

    /**
     * Gets a method's details
     *
     * @param    string            $visibility Method visibility
     * @param    \ReflectionMethod $method     Method's \ReflectionMethod instance
     * @return    MethodData
     */
    private function getMethodDetails($visibility, \ReflectionMethod $method)
    {
        $details = new MethodData();
        $details->name = $method->getName();
        $details->visibility = $visibility;
        $details->isSetter = (preg_match('/^set/', $method->getName()) === 1) ? true : false;
        $details->arguments = $this->argumentWorker->generateArgumentObjects($method);

        return $details;
    }
}

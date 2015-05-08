<?php

/**
 * ArgumentDataWorker
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 29, 2015
 * @version       1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ArgumentData;

class ArgumentDataWorker
{

    /**
     * Generates an array of ArgumentData objects
     *
     * @param    \ReflectionMethod $method Method's ReflectionMethod instance
     * @return  array
     */
    public function generateArgumentObjects(\ReflectionMethod $method)
    {
        $details = [];
        $arguments = $method->getParameters();

        if (empty($arguments)) {
            return $details;
        }

        foreach ($arguments as $arg) {
            array_push($details, $this->getArgumentDetails($arg));
        }

        return $details;
    }

    /**
     * Gets the details for a single method argument
     *
     * @param    \ReflectionParameter $argument Argument's ReflectionParameter instance
     * @return    ArgumentData
     */
    private function getArgumentDetails(\ReflectionParameter $argument)
    {
        $details = new ArgumentData();
        $details->name = $argument->getName();
        $details->passedByReference = $argument->isPassedByReference();
        // true if no typehinting or typehinted argument defaults to null
        $details->allowsNull = $argument->allowsNull();
        $details->dataType = $this->getArgumentType($argument);
        if ($details->dataType === 'object') {
            $classData = $this->getDefaultValueClassData($argument->__toString());
            $details->className = $classData['className'];
            $details->classNamespace = $classData['classNamespace'];
        }
        if ($argument->isOptional()) {
            $details->isRequired = false;
            $details->defaultValue = $argument->getDefaultValue();
        }

        return $details;
    }

    /**
     * Gets the argument data type
     *
     * Returns same data types as PHP's gettype() method:
     *  'boolean', 'integer', 'double', 'string', 'array',
     *  'object', 'resource', 'NULL', 'unknown type'
     *
     * @param    \ReflectionParameter $argument Argument's ReflectionParameter instance
     * @return    string
     */
    private function getArgumentType(\ReflectionParameter $argument)
    {
        if ($argument->isArray()) {
            return 'array';
        }
        // check to see if it's a typehinted class
        $regex = '/^.*\<\w+?> ([\w\\\\]+?) +.*$/';
        preg_match($regex, $argument->__toString(), $matches);
        if (isset($matches[1])) {
            return 'object';
        }
        if ($argument->isOptional()) {
            return gettype($argument->getDefaultValue());
        }

        return null;
    }

    /**
     * Gets the typehinted arguments class data
     *
     * @param   string $toString \ReflectionArgument __toString() value
     * @return  array
     */
    private function getDefaultValueClassData($toString)
    {
        $data = array('className' => '', 'classNamespace' => '');
        $regex = '/^.*\<\w+?> ([\w\\\\]+?) +.*$/';
        preg_match($regex, $toString, $matches);
        if (isset($matches[1])) {
            $className = $matches[1];
            if (($pos = strrpos($className, '\\')) === false) {
                $data['className'] = $className;
                $data['classNamespace'] = "";
            } else {
                $data['className'] = substr($className, $pos + 1);
                $data['classNamespace'] = substr($className, 0, $pos);
            }
        }

        return $data;
    }
}

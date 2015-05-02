<?php
/**
 * ReflectionHelper
 *
 * This class assists with quick access to private/protected
 * methods/properties via reflection.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        4/30/15
 * @version        1.0
 */

namespace MockMaker\Helper;

class ReflectionHelper
{

    /**
     * This uses reflection to set the value of a private/protected parameter
     * to a desired value.
     *
     * Unless you're changing the values of a Mockery/Prophecy created object,
     * you can usually pass the same object as the first 2 parameters.
     *
     * Warning! Uses pass-by-reference!
     *
     * @param    $objToChange          object    Object whose property you want to change.
     * @param    $objToReflect         mixed    Object to use as a base for inspecting the property.
     * @param    $property             string    Name of the property you want to change.
     * @param    $value                string    Value to set the property to.
     */
    public static function setNonPublicValue(&$objToChange, $objToReflect, $property, $value)
    {
        $class = (is_object($objToReflect)) ? get_class($objToReflect) : $objToReflect;
        $refClass = new \ReflectionClass($class);
        $refProp = $refClass->getProperty($property);
        $refProp->setAccessible(true);
        $refProp->setValue($objToChange, $value);
    }

    /**
     * Make non-public methods accessible.
     *
     * $actual = $method->invoke( <class>, <param1>, [<param2>...] );
     *
     * @param    $class             string    Class you want to access method in.
     * @param    $methodName        string    Method you want to make public.
     * @return    \ReflectionMethod
     */
    public static function getAccessibleNonPublicMethod($class, $methodName)
    {
        $className = (is_object($class)) ? get_class($class) : $class;
        $reflection = new \ReflectionClass($className);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
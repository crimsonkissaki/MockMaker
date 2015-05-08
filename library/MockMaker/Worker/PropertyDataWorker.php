<?php

/**
 * PropertyDataWorker
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 28, 2015
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\PropertyData;

class PropertyDataWorker
{

    /**
     * Instance of the mocked class
     *
     * @var object
     */
    private $classInstance;

    /**
     * Array of the class's methods in MethodData form
     *
     * @var array
     */
    private $methods = [];

    /**
     * Generates an array of PropertyData objects for a class
     *
     * @param   string $className       Name of target class
     * @param   array  $classProperties Array of \ReflectionProperty objects
     * @param   array  $methods         Array of MethodData objects.
     * @return  array
     */
    public function generatePropertyObjects($className, array $classProperties, array $methods)
    {
        $this->classInstance = new $className();
        $this->methods = $methods;

        return $this->getClassPropertiesDetails($classProperties);
    }

    /**
     * Gets an array of class properties through a reflection class instance
     *
     * @param   \ReflectionClass $class
     * @return  array
     */
    public function getClassProperties(\ReflectionClass $class)
    {
        $classProperties = [];
        $classProperties['constant'] = $class->getConstants();
        $classProperties['private'] = $class->getProperties(\ReflectionProperty::IS_PRIVATE);
        $classProperties['protected'] = $class->getProperties(\ReflectionProperty::IS_PROTECTED);
        $classProperties['public'] = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        $classProperties['static'] = $class->getProperties(\ReflectionProperty::IS_STATIC);

        return $classProperties;
    }

    /**
     * Gets an array of PropertyData objects for the class's properties
     *
     * @param   array $classProperties Array of \ReflectionProperties
     * @return  array
     */
    private function getClassPropertiesDetails($classProperties)
    {
        $propertyObjects = [];
        foreach ($classProperties as $visibility => $properties) {
            if (!empty($properties)) {
                $propertyObjects[$visibility] = $this->getPropertiesDetails($visibility, $properties);
            }
        }

        return $propertyObjects;
    }

    /**
     * Gets details for an array of properties
     *
     * Constants are a problem here since they're set as an associative array
     * and not numeric, so 'as key => value' works while 'as $property' does not.
     *
     * @param   string $visibility Visibility scope of properties
     * @param   array  $properties Array of \ReflectionProperties objects
     * @return  array
     */
    private function getPropertiesDetails($visibility, $properties)
    {
        $results = [];
        foreach ($properties as $key => $value) {
            // it causes problems down the line to have static
            // properties set in the other visibilities
            $prop = $this->getPropertyDetails($visibility, $key, $value);
            if ($visibility !== 'static' && empty($prop->isStatic)) {
                array_push($results, $prop);
            }
            if ($visibility === 'static') {
                array_push($results, $prop);
            }
        }

        return $results;
    }

    /**
     * Gets details for a single class property
     *
     * Looping through an array of all class properties returned by
     * a \ReflectionClass's getProperties() method returns is interesting
     * because 'constant' properties are returned as an associative array
     * of 'name' => 'value', while every other visibility level is returned
     * as a numerically indexed array of 'N' => \ReflectionProperty objects.
     *
     * @param    string        $visibility Property visibility
     * @param    string|int    $key        Property name or a numeric index
     * @param    string|object $value      Property value or \ReflectionProperty object
     * @return  PropertyData
     */
    private function getPropertyDetails($visibility, $key, $value)
    {
        $result = new PropertyData();
        $result->name = (is_object($value)) ? $value->name : $key;
        $result->visibility = $visibility;
        $result->isStatic = (is_object($value)) ? $value->isStatic() : false;
        $result->defaultValue = $this->getPropertyDefaultValue($visibility, $value);
        // this will require the class methods
        $result->setter = $this->findPropertySetterIfExists($result->name, $this->methods);
        $result->dataType = gettype($result->defaultValue);
        if (is_object($result->defaultValue)) {
            $classData = $this->getDefaultValueClassData($result->defaultValue);
            $result->className = $classData['className'];
            $result->classNamespace = $classData['classNamespace'];
        }

        return $result;
    }

    /**
     * Gets the default value for a property, if any
     *
     * 'Constant' properties just need the value string returned.
     * All other visibility levels are a \ReflectionProperty object
     * whose getValue() method requires an actual instance of the
     * owning class to determine the default value.
     *
     * @param   string        $visibility Property visibility
     * @param   string|object $value      String or \ReflectionProperty object
     * @return  string
     */
    private function getPropertyDefaultValue($visibility, $value)
    {
        if ($visibility === 'constant') {
            return $value;
        }
        if ($value->isPrivate() || $value->isProtected()) {
            $value->setAccessible(true);
        }

        // do not have to pass a class instance if you use ReflectionClass::getDefaultProperties
        return (is_object($this->classInstance)) ? $value->getValue($this->classInstance) : 'unknown';
    }

    /**
     * Gets the name of a property's setter, if one exists
     *
     * This method has to make the assumption that a property's
     * setter will be named after it, e.g. a property called "totalCount"
     * has a setter with the name "setTotalCount".
     *
     * @param   string $name         Property name
     * @param   array  $classMethods Array of MethodData objects
     * @return  array
     */
    private function findPropertySetterIfExists($name, $classMethods)
    {
        foreach ($classMethods as $methods) {
            if (!empty($methods)) {
                foreach ($methods as $method) {
                    if (stripos($method->name, "set{$name}") !== false) {
                        return $method->name;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Gets class data for default values that are objects
     *
     * This method consumes the return value of a \ReflectionProperty
     * object's getValue() method, and if the default value is an object
     * getValue() returns an instance of that object.
     *
     * @param   object $defaultValue Instance of a default value object
     * @return  array
     */
    private function getDefaultValueClassData($defaultValue)
    {
        $className = get_class($defaultValue);
        if (($pos = strrpos($className, '\\')) === false) {
            $data['className'] = $className;
            $data['classNamespace'] = "";
        } else {
            $data['className'] = substr($className, $pos + 1);
            $data['classNamespace'] = substr($className, 0, $pos);
        }

        return $data;
    }
}

<?php

/**
 * 	PropertyDataWorker
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\PropertyData;
use MockMaker\Helper\TestHelper;

class PropertyDataWorker
{

    /**
     * Instance of the class we're mocking.
     *
     * @var object
     */
    private $classInstance;

    /**
     * \ReflectionClass instance of the class we're mocking.
     *
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * Array of the class's methods in MethodData form.
     *
     * @var array
     */
    private $methods = [ ];

    /**
     * Associative array of PropertyData objects in visibility => array( properties ) format.
     *
     * array(
     *   'public' => array( ... ),
     *   'private' => array( ... ),
     * )
     *
     * @var array
     */
    private $classPropertyObjects = [ ];

    /**
     * Generate an array of PropertyData objects for a class.
     *
     * @param   $class      \ReflectionClass    Reflection of the class to be mocked.
     * @param   $methods    array               Array of MethodData objects.
     * @return  array
     */
    public function generatePropertyObjects(\ReflectionClass $class, $methods)
    {
        $this->reflectionClass = $class;
        $className = $class->getName();
        $this->classInstance = new $className();
        $this->methods = $methods;
        $classProperties = $this->getClassPropertiesByVisibility($class);
        $this->classPropertyObjects = $this->getClassPropertiesDetails($classProperties);

        return $this->classPropertyObjects;
    }

    /**
     * Get class properties by visibility.
     *
     * @param   $class  \ReflectionClass
     * @return  array
     */
    private function getClassPropertiesByVisibility(\ReflectionClass $class)
    {
        $classProperties = [ ];
        $classProperties['constant'] = $class->getConstants();
        $classProperties['private'] = $class->getProperties(\ReflectionProperty::IS_PRIVATE);
        $classProperties['protected'] = $class->getProperties(\ReflectionProperty::IS_PROTECTED);
        $classProperties['public'] = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        //$classProperties['static'] = $class->getProperties(\ReflectionProperty::IS_STATIC);
        return $classProperties;
    }

    /**
     * Get an array of PropertyData objects for the class properties.
     *
     * @param   $classProperties     array  Array of \ReflectionProperties
     * @return  array
     */
    private function getClassPropertiesDetails($classProperties)
    {
        $propertyObjects = [ ];
        foreach ($classProperties as $visibility => $properties) {
            if (!empty($properties)) {
                $propertyObjects[$visibility] = $this->getPropertiesDetails($visibility, $properties);
            }
        }

        return $propertyObjects;
    }

    /**
     * Get details for an array of properties.
     *
     * Constants are a problem here since they're set as an associative array, not numeric.
     * So we have to use 'as key => value' instead of 'as $property'
     *
     * @param   $visibility     string
     * @param   $properties     array   Array of \ReflectionProperties
     * @return  array
     */
    private function getPropertiesDetails($visibility, $properties)
    {
        $results = [ ];
        foreach ($properties as $key => $value) {
            array_push($results, $this->getPropertyDetails($visibility, $key, $value));
        }

        return $results;
    }

    /**
     * Get details for a single class property.
     *
     * @param	$visibility     string		Public/private/protected/static/etc
     * @param	$key	        mixed		String or int array key
     * @param	$value	        mixed		String or \ReflectionProperty object
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
     * Get the default value for a property, if any.
     *
     * @param   $visibility     string      Public/Private/Protected
     * @param   $value          mixed       String (if const) or \ReflectionProperty
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
     * Get the name of a property's setter, if one exists.
     *
     * This method has to make the assumption that a property's
     * setter will be named after it. E.g. a property called "totalCount"
     * has a setter with the name "setTotalCount".
     *
     * @param	$name	        string	Property name
     * @param   $classMethods   array   Property methods
     * @return	mixed
     */
    private function findPropertySetterIfExists($name, $classMethods)
    {
        foreach ($classMethods as $visibility => $methods) {
            if (!empty($methods)) {
                foreach ($methods as $k => $method) {
                    if (stripos($method->name, "set{$name}") !== false) {
                        return $method->name;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get class data for an object default value
     *
     * @param   $defaultValue   object
     * @return  array
     */
    private function getDefaultValueClassData($defaultValue)
    {
        $className = get_class($defaultValue);
        if (($pos = strrpos($className, '\\') ) === false) {
            $data['className'] = "\\{$className}";
            $data['classNamespace'] = "";
        } else {
            $data['className'] = substr($className, $pos + 1);
            $data['classNamespace'] = substr($className, 0, $pos);
        }

        return $data;
    }

}

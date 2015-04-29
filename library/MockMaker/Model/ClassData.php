<?php

/**
 * 	ClassData
 *
 *  Class model that holds all class-specific information.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

class ClassData
{

    /**
     * Class namespace
     *
     * @var string
     */
    private $classNamespace;

    /**
     * Class name
     *
     * @var string
     */
    private $className;

    /**
     * Reflection class instance.
     *
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * Is this a concrete/abstract/interface/final class
     *
     * @var string
     */
    private $classType = 'concrete';

    /**
     * Class use statements.
     *
     * @var array
     */
    private $useStatements = [ ];

    /**
     * Associative array of any classes implemented by the target class, if any.
     *
     * implements = array(
     *   array(
     *     'className' => class,
     *     'classNamespace' => namespace
     *   ),
     *   ...
     * )
     *
     * @var array
     */
    private $implements = [ ];

    /**
     * Associative array of the class extended by the target class, if any.
     *
     * extends = array(
     *   'className' => class,
     *   'classNamespace' => namespace
     * )
     *
     * @var array
     */
    private $extends = [ ];

    /**
     * Does the class have a constructor.
     *
     * @var bool
     */
    private $hasConstructor = false;

    /**
     * Array of MethodData objects.
     *
     * @var array
     */
    private $methods = [ ];

    /**
     * Array of PropertyData objects.
     *
     * @var array
     */
    private $properties = [ ];

    /**
     * Get the class's namespace.
     *
     * @return  string
     */
    public function getClassNamespace()
    {
        return $this->classNamespace;
    }

    /**
     * Get the class's name.
     *
     * @return  string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get the reflection class instance.
     *
     * @return  \ReflectionClass
     */
    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    /**
     * Get the class type - concrete/abstract/interface/final.
     *
     * @return  string
     */
    public function getClassType()
    {
        return $this->classType;
    }

    /**
     * Get the class's use statements.
     *
     * @return array
     */
    public function getUseStatements()
    {
        return $this->useStatements;
    }

    /**
     * Get an array of classes the class implements.
     *
     * @return  array
     */
    public function getImplements()
    {
        return $this->implements;
    }

    /**
     * Get an array of classes the class extends.
     *
     * @return  array
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * Get if the class has a constructor.
     *
     * @return  bool
     */
    public function getHasConstructor()
    {
        return $this->hasConstructor;
    }

    /**
     * Get the array of MethodData objects
     *
     * @return  array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get the array of PropertyData objects
     *
     * @return  array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set the class's name space.
     *
     * @param   $classNamespace     string
     * @return  ClassData
     */
    public function setClassNamespace($classNamespace)
    {
        $this->classNamespace = $classNamespace;

        return $this;
    }

    /**
     * Set the class's name.
     *
     * @param   $className     string
     * @return  ClassData
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Set the reflection class instance.
     *
     * @param   $reflectionClass    \ReflectionClass
     * @return  ClassData
     */
    public function setReflectionClass(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;

        return $this;
    }

    /**
     * Set the class type - concrete/abstract/interface/final.
     *
     * @param   $classType  string
     * @return  ClassData
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;

        return $this;
    }

    /**
     * Set the class's use statements.
     *
     * @param   $useStatements  array
     * @return  ClassData
     */
    public function setUseStatements($useStatements)
    {
        $this->useStatements = $useStatements;

        return $this;
    }

    /**
     * Add a single or array of use statements to useStatements.
     *
     * @param   $useStatements  mixed
     * @return  ClassData
     */
    public function addUseStatements($useStatements)
    {
        if (is_array($useStatements)) {
            $this->setUseStatements(array_merge($this->useStatements, $useStatements));
        } else {
            array_push($this->useStatements, $useStatements);
        }

        return $this;
    }

    /**
     * Set an array of classes the class implements.
     *
     * @param   $implements  array
     * @return  ClassData
     */
    public function setImplements($implements)
    {
        $this->implements = $implements;

        return $this;
    }

    /**
     * Add (single|array of) classes the class implements.
     *
     * @param   $implements  mixed
     * @return  ClassData
     */
    public function addImplements($implements)
    {
        if (is_array($implements)) {
            $this->setImplements(array_merge($this->implements, $implements));
        } else {
            array_push($this->implements, $implements);
        }

        return $this;
    }

    /**
     * Set an array of classes the class extends.
     *
     * @param   $extends    array
     * @return  ClassData
     */
    public function setExtends($extends)
    {
        $this->extends = $extends;

        return $this;
    }

    /**
     * Set if the class has a constructor.
     *
     * @param   $hasConstructor bool
     * @return  ClassData
     */
    public function setHasConstructor($hasConstructor)
    {
        $this->hasConstructor = $hasConstructor;

        return $this;
    }

    /**
     * Set the array of MethodData objects
     *
     * @param   $methods    array
     * @return  ClassData
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * Add (single|array of) MethodData objects to methods array.
     *
     * @param   $methods    mixed
     * @return  ClassData
     */
    public function addMethods($methods)
    {
        if (is_array($methods)) {
            $this->setMethods(array_merge($this->methods, $methods));
        } else {
            array_push($this->methods, $methods);
        }

        return $this;
    }

    /**
     * Set the array of PropertyData objects
     *
     * @param   $properties     array
     * @return  ClassData
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Add (single|array of) PropertyData objects to properties array.
     *
     * @param   $properties     mixed
     * @return  ClassData
     */
    public function addProperties($properties)
    {
        if (is_array($properties)) {
            $this->setProperties(array_merge($this->properties, $properties));
        } else {
            array_push($this->properties, $properties);
        }

        return $this;
    }

}

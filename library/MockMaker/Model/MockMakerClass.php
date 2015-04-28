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
     * Does the class have a constructor.
     *
     * @var bool
     */
    private $hasConstructor = false;

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
     * Get if the class has a constructor.
     *
     * @return  bool
     */
    public function getHasConstructor()
    {
        return $this->hasConstructor;
    }

    /**
     * Set the class's name space.
     *
     * @param   $classNamespace     string
     * @return  MockMakerClass
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
     * @return  MockMakerClass
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
     * @return  MockMakerClass
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
     * @return  MockMakerClass
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
     * @return  MockMakerClass
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
     * @return  MockMakerClass
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
     * Set if the class has a constructor.
     *
     * @param   $hasConstructor bool
     * @return  MockMakerClass
     */
    public function setHasConstructor($hasConstructor)
    {
        $this->hasConstructor = $hasConstructor;

        return $this;
    }

}

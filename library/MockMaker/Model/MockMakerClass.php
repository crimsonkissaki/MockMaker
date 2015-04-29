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
     * Set an array of classes the class implements.
     *
     * @param   $implements  array
     * @return  MockMakerClass
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
     * @return  MockMakerClass
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
     * @return  MockMakerClass
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
     * @return  MockMakerClass
     */
    public function setHasConstructor($hasConstructor)
    {
        $this->hasConstructor = $hasConstructor;

        return $this;
    }

}

<?php

/**
 * EntityData
 *
 * Object that holds all target class analysis and information.
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

class EntityData
{

    /**
     * FileData object
     *
     * @var FileData
     */
    private $fileData;

    /**
     * Entity class name
     *
     * @var string
     */
    private $className;

    /**
     * Entity class namespace
     *
     * @var string
     */
    private $classNamespace;

    /**
     * Entity class \ReflectionClass instance.
     *
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * Is mocked class concrete/abstract/interface/final
     *
     * @var string
     */
    private $classType = 'concrete';

    /**
     * Entity class use statements
     *
     * @var array
     */
    private $useStatements = [];

    /**
     * Associative array of mocked class's implemented classes
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
    private $implements = [];

    /**
     * Associative array of mocked class's extended class
     *
     * extends = array(
     *   'className' => class,
     *   'classNamespace' => namespace
     * )
     *
     * @var array
     */
    private $extends = [];

    /**
     * Does the mocked class have a constructor
     *
     * @var bool
     */
    private $hasConstructor = false;

    /**
     * Entity class's method details in array of \ReflectionMethod objects
     *
     * @var array
     */
    private $methods = [];

    /**
     * Entity class's property details in array of \ReflectionProperty objects
     *
     * @var array
     */
    private $properties = [];

    /**
     * Gets the entity's FileData object
     *
     * @return FileData
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    /**
     * Gets the mocked class's name
     *
     * @return  string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Gets the mocked class's namespace
     *
     * @return  string
     */
    public function getClassNamespace()
    {
        return $this->classNamespace;
    }

    /**
     * Get the mocked class's ReflectionClass instance
     *
     * @return  \ReflectionClass
     */
    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    /**
     * Gets the mocked class's type
     *
     * @return  string
     */
    public function getClassType()
    {
        return $this->classType;
    }

    /**
     * Gets the mocked class's use statements
     *
     * @return array
     */
    public function getUseStatements()
    {
        return $this->useStatements;
    }

    /**
     * Gets the mocked class's implemented classes
     *
     * @return  array
     */
    public function getImplements()
    {
        return $this->implements;
    }

    /**
     * Gets the mocked class's extended class
     *
     * @return  array
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * Gets if the mocked class has a constructor
     *
     * @return  bool
     */
    public function getHasConstructor()
    {
        return $this->hasConstructor;
    }

    /**
     * Gets the mocked class's \ReflectionMethod objects
     *
     * @return  array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Gets the mocked class's \ReflectionProperty objects
     *
     * @return  array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Sets the entity's FileData object
     *
     * @param   FileData $fileData
     * @return  EntityData
     */
    public function setFileData(FileData $fileData)
    {
        $this->fileData = $fileData;

        return $this;
    }

    /**
     * Sets the mocked class's name
     *
     * @param   string $className Target class's name
     * @return  EntityData
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Sets the mocked class's name space
     *
     * @param   string $classNamespace Target class's namespace
     * @return  EntityData
     */
    public function setClassNamespace($classNamespace)
    {
        $this->classNamespace = $classNamespace;

        return $this;
    }

    /**
     * Sets the mocked class's ReflectionClass instance
     *
     * @param   \ReflectionClass $reflectionClass Reflection class
     * @return  EntityData
     */
    public function setReflectionClass(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;

        return $this;
    }

    /**
     * Sets the mocked class's type
     *
     * Valid values are concrete, abstract, interface, final.
     *
     * @param   string $classType Class type
     * @return  EntityData
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;

        return $this;
    }

    /**
     * Sets  the mocked class's use statements
     *
     * @param   array $useStatements Array of use statements in target class file
     * @return  EntityData
     */
    public function setUseStatements($useStatements)
    {
        $this->useStatements = $useStatements;

        return $this;
    }

    /**
     * Adds (single|array of) use statements to useStatements
     *
     * @param   mixed $useStatements Use statements
     * @return  EntityData
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
     * Sets the array of classes the class implements
     *
     * @param   array $implements Classes the target class implements
     * @return  EntityData
     */
    public function setImplements($implements)
    {
        $this->implements = $implements;

        return $this;
    }

    /**
     * Adds (single|array of) classes the class implements
     *
     * @param   string|array $implements Classes the target class implements
     * @return  EntityData
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
     * Sets the class the mocked class extends
     *
     * @param   string $extends Class the target class extends
     * @return  EntityData
     */
    public function setExtends($extends)
    {
        $this->extends = $extends;

        return $this;
    }

    /**
     * Sets if the class has a constructor
     *
     * @param   bool $hasConstructor Does the class have a constructor
     * @return  EntityData
     */
    public function setHasConstructor($hasConstructor)
    {
        $this->hasConstructor = $hasConstructor;

        return $this;
    }

    /**
     * Sets the array of \ReflectionMethod objects
     *
     * @param   object|array $methods \ReflectionMethod objects
     * @return  EntityData
     */
    public function setMethods($methods)
    {
        $objs = is_array($methods) ? $methods : array($methods);
        $this->methods = $objs;

        return $this;
    }

    /**
     * Adds (single|array of) \ReflectionMethod objects to methods array
     *
     * @param   object|array $methods \ReflectionMethod objects
     * @return  EntityData
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
     * Sets the array of \ReflectionProperty objects
     *
     * @param   object|array $properties \ReflectionProperty objects
     * @return  EntityData
     */
    public function setProperties($properties)
    {
        $objs = is_array($properties) ? $properties : array($properties);
        $this->properties = $objs;

        return $this;
    }

    /**
     * Add (single|array of) \ReflectionProperty objects to properties array
     *
     * @param   object|array $properties \ReflectionProperty objects
     * @return  EntityData
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

    public function __construct()
    {
        $this->fileData = new FileData();
    }
}

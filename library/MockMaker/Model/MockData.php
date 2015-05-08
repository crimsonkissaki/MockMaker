<?php
/**
 * MockData
 *
 * Holds all data required for generating mock files.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/6/15
 * @version        1.0
 */

namespace MockMaker\Model;

class MockData
{

    /**
     * FileData object for mock
     *
     * @var FileData
     */
    private $fileData;

    /**
     * FileData object for mock unit tests
     *
     * @var FileData
     */
    private $utFileData;

    /**
     * File name of mock file
     *
     * @var string
     */
    private $className;

    /**
     * Namespace of the mock file
     *
     * @var string
     */
    private $classNamespace;

    /**
     * Namespace of the mock file unit test file
     *
     * @var string
     */
    private $utClassNamespace;

    /**
     * Mocked class use statements
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
     * Mocked class's method details in array of MethodData objects
     *
     * @var array
     */
    private $methods = [];

    /**
     * Mocked class's property details in array of PropertyData objects
     *
     * @var array
     */
    private $properties = [];

    /**
     * Gets the FileData object
     *
     * @return FileData
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    /**
     * Gets the unit test file data object
     *
     * @return FileData
     */
    public function getUtFileData()
    {
        return $this->utFileData;
    }

    /**
     * Gets the mock's file name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Gets the mock file's namespace
     *
     * @return string
     */
    public function getClassNamespace()
    {
        return $this->classNamespace;
    }

    /**
     * Gets the mock file unit test's namespace
     *
     * @return string
     */
    public function getUtClassNamespace()
    {
        return $this->utClassNamespace;
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
     * Gets the mocked class's MethodData objects
     *
     * @return  array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Gets the mocked class's PropertyData objects
     *
     * @return  array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Sets the FileData object
     *
     * @param   FileData $fileData
     * @return  MockData
     */
    public function setFileData($fileData)
    {
        $this->fileData = $fileData;

        return $this;
    }

    /**
     * Sets the unit test file data object
     *
     * @param   FileData $utFileData
     * @return  MockData
     */
    public function setUtFileData($utFileData)
    {
        $this->utFileData = $utFileData;

        return $this;
    }

    /**
     * Sets the mock's file name
     *
     * @param   string $className
     * @return  MockData
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Sets the mock file's namespace
     *
     * @param   string $classNamespace
     * @return  MockData
     */
    public function setClassNamespace($classNamespace)
    {
        $this->classNamespace = $classNamespace;

        return $this;
    }

    /**
     * Sets the mock file unit test's namespace
     *
     * @param   string $utClassNamespace
     * @return  MockData
     */
    public function setUtClassNamespace($utClassNamespace)
    {
        $this->utClassNamespace = $utClassNamespace;

        return $this;
    }

    public function __construct()
    {
        $this->fileData = new FileData();
        $this->utFileData = new FileData();
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
     * Sets the array of MethodData objects
     *
     * @param   object|array $methods MethodData objects
     * @return  EntityData
     */
    public function setMethods($methods)
    {
        $objs = is_array($methods) ? $methods : array($methods);
        $this->methods = $objs;

        return $this;
    }

    /**
     * Adds (single|array of) MethodData objects to methods array
     *
     * @param   object|array $methods MethodData objects
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
     * Sets the array of PropertyData objects
     *
     * @param   object|array $properties PropertyData objects
     * @return  EntityData
     */
    public function setProperties($properties)
    {
        $objs = is_array($properties) ? $properties : array($properties);
        $this->properties = $objs;

        return $this;
    }

    /**
     * Add (single|array of) PropertyData objects to properties array
     *
     * @param   object|array $properties PropertyData objects
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
}
<?php

/**
 * EntityDataWorker
 *
 * Analyzes a target class for data needed in mock files.
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;
use MockMaker\Model\EntityData;
use MockMaker\Exception\MockMakerException;
use MockMaker\Exception\MockMakerErrors;

class EntityDataWorker
{

    /**
     * Array of valid namespaces found during processing
     *
     * @var array
     */
    private $validNamespaces = [];

    /**
     * File token worker class
     *
     * @var TokenWorker
     */
    private $tokenWorker;

    /**
     * Class that generates MethodData objects
     *
     * @var MethodDataWorker
     */
    private $methodWorker;

    /**
     * Class that generates PropertyData objects
     *
     * @var PropertyDataWorker
     */
    private $propertyWorker;

    /**
     * Gets the array of valid namespaces
     *
     * @return  array
     */
    public function getValidNamespaces()
    {
        return $this->validNamespaces;
    }

    /**
     * Adds namespaces to the validNamespaces array
     *
     * @param   string|array $validNamespaces Namespace strings
     * @return  void
     */
    public function addValidNamespaces($validNamespaces)
    {
        $namespaces = (is_array($validNamespaces)) ? $validNamespaces : array($validNamespaces);
        foreach ($namespaces as $namespace) {
            if (!in_array($namespace, $this->validNamespaces)) {
                array_push($this->validNamespaces, $namespace);
            }
        }
    }

    /**
     * Creates a new instance of EntityDataWorker
     *
     * @return  EntityDataWorker
     */
    public function __construct()
    {
        $this->tokenWorker = new TokenWorker();
        $this->propertyWorker = new PropertyDataWorker();
        $this->methodWorker = new MethodDataWorker();
    }

    /**
     * Generates a EntityData object
     *
     * @param   string     $file   Fully qualified path to target file
     * @param   ConfigData $config ConfigData object
     * @return  EntityData
     * @throws  MockMakerException
     */
    public function generateEntityDataObject($file, ConfigData $config)
    {
        $obj = $this->getBasicEntityInformation($file, $config);
        $this->canWeContinueProcessing($obj);
        $obj = $this->getClassDetails($obj);

        return $obj;
    }

    /**
     * Checks for entity types that cannot be instantiated for analysis and mocking
     *
     * @param   EntityData  $entity
     * @return  bool
     * @throws  MockMakerException
     */
    private function canWeContinueProcessing(EntityData $entity)
    {
        // We cannot mock abstract or interface classes
        $args = array('class' => $entity->getClassName());
        if (in_array($entity->getClassType(), array('abstract', 'interface'))) {
            throw new MockMakerException(
                MockMakerErrors::generateMessage(MockMakerErrors::INVALID_CLASS_TYPE, $args)
            );
        }
        // constructors with arguments are bad news bears
        if (!$this->isEntityInstantiable($entity)) {
            throw new MockMakerException(
                MockMakerErrors::generateMessage(MockMakerErrors::CLASS_HAS_CONSTRUCTOR, $args)
            );
        }

        return true;
    }

    /**
     * Determines if a target entity class can be instantiated into a \ReflectionClass object
     *
     * Classes with constructors pose a big problem to reflection analysis. Eventually I might
     * do something with Mockery here to fake the constructor arguments, but so far this is
     * only a problem when encountering Repository type classes for Doctrine. Since those
     * are not really entities that need to be mocked, I'm choosing to flat out ignore them.
     *
     * @param   EntityData $entity
     * @return  bool
     */
    private function isEntityInstantiable(EntityData $entity)
    {
        $constructor = $entity->getReflectionClass()->getConstructor();
        if(is_null($constructor)) {
            return true;
        }

        return (empty($constructor->getParameters())) ? true : false;
    }

    /**
     * Creates and populates a EntityData object with the basic information it needs
     *
     * @param   string     $file
     * @param   ConfigData $config
     * @return  EntityData
     * @throws  MockMakerException
     */
    private function getBasicEntityInformation($file, ConfigData $config)
    {
        $obj = new EntityData();
        $obj->getFileData()->setFileName(PathWorker::getLastElementInPath($file))
            ->setFileDirectory(PathWorker::getPathUpToName($file) . '/')
            ->setFullFilePath($file);

        $obj->setClassName(PathWorker::getClassNameFromFilePath($file))
            ->setClassNamespace($this->determineClassNamespace($file, $config->getProjectRootPath()))
            ->setReflectionClass($this->createReflectionClass($obj))
            ->setClassType($this->getClassType($obj->getReflectionClass()));

        return $obj;
    }

    /**
     * Gathers the target class details
     *
     * @param   EntityData $obj
     * @return  EntityData
     */
    private function getClassDetails(EntityData $obj)
    {
        $obj->setHasConstructor($this->hasConstructor($obj->getReflectionClass()))
            ->addUseStatements($this->getClassUseStatements($obj->getFileData()->getFullFilePath()))
            ->setExtends($this->getExtendsClass($obj->getReflectionClass()))
            ->addImplements($this->getImplementsClasses($obj->getReflectionClass()))
            ->addMethods($this->methodWorker->getClassMethods($obj->getReflectionClass()))
            ->addProperties($this->propertyWorker->getClassProperties($obj->getReflectionClass()));

        return $obj;
    }

    /**
     * Determines a class's namespace by iterating over a filepath=>namespace conversion
     *
     * @param   string $filePath Fully qualified target file path
     * @param   string $rootPath Project root path
     * @return  string
     * @throws  MockMakerException
     */
    private function determineClassNamespace($filePath, $rootPath)
    {
        $className = PathWorker::getClassNameFromFilePath($filePath);
        if ($result = $this->checkClassUsingValidNamespacesArray($className)) {
            return $result;
        }
        // not already in our array, so we have to find it the hard way
        $classPath = PathWorker::convertFilePathToClassPath($filePath, $rootPath);
        if (!$result = $this->getClassNamespaceFromFilePath($classPath)) {
            $args = array('class' => $className);
            throw new MockMakerException(
                MockMakerErrors::generateMessage(MockMakerErrors::INVALID_CLASS_TYPE, $args)
            );
        }
        $this->addValidNamespaces($result);

        return $result;
    }

    /**
     * Iterates through the array of currently known valid namespaces for the class
     *
     * @param   string $className Class's short name
     * @return  string
     */
    private function checkClassUsingValidNamespacesArray($className)
    {
        foreach ($this->validNamespaces as $namespace) {
            $possible = "{$namespace}\\{$className}";
            if (class_exists($possible)) {
                return $namespace;
            }
        }

        return false;
    }

    /**
     * Recurses over a filepath => namespace converted string in an attempt
     * to find a valid class namespace path
     *
     * @param   string $classPath File path converted to class path
     * @return  string|bool
     */
    private function getClassNamespaceFromFilePath($classPath)
    {
        if (!class_exists($classPath)) {
            if (($pos = strpos($classPath, '\\')) === false) {
                return false;
            }

            return $this->getClassNamespaceFromFilePath(substr($classPath, ($pos + 1)));
        }

        return PathWorker::getPathUpToName($classPath, '\\');
    }

    /**
     * Creates a ReflectionClass instance of the target class
     *
     * TODO: Eventually this needs to be able to handle classes with constructors as well.
     *
     * Might need to use Mockery to generate mocked versions of the constructor arguments
     * so this can continue.
     *
     * PHP >= 5.4 has the ReflectionClass::newInstanceWithoutConstructor method
     * Otherwise ... parse the file directly?
     *
     * @param   EntityData $classData
     * @return  \ReflectionClass
     */
    private function createReflectionClass(EntityData $classData)
    {
        return new \ReflectionClass($classData->getClassNamespace() . '\\' . $classData->getClassName());
    }

    /**
     * Gets the class's type
     *
     * Values can be concrete, abstract, interface, or final.
     *
     * @param   \ReflectionClass $class ReflectionClass instance of target class
     * @return  string
     */
    private function getClassType(\ReflectionClass $class)
    {
        if ($class->isFinal()) {
            return 'final';
        }
        if ($class->isAbstract()) {
            return 'abstract';
        }
        if ($class->isInterface()) {
            return 'interface';
        }

        return 'concrete';
    }

    /**
     * Determines if the class has a constructor or not
     *
     * @param   \ReflectionClass $class ReflectionClass instance of target class
     * @return  bool
     */
    private function hasConstructor(\ReflectionClass $class)
    {
        if (!is_null($class->getConstructor())) {
            return true;
        }

        return false;
    }

    /**
     * Get any use statements in the file
     *
     * @param   $file   string
     * @return  array
     */
    private function getClassUseStatements($file)
    {
        return $this->tokenWorker->getUseStatementsWithTokens($file);
    }

    /**
     * Gets information on the extended class for the target class, if any
     *
     * @param   \ReflectionClass $reflectionClass ReflectionClass instance of target class
     * @return  array
     */
    private function getExtendsClass(\ReflectionClass $reflectionClass)
    {
        $extends = [];
        if ($parent = $reflectionClass->getParentClass()) {
            $extends = array(
                'className'      => $parent->getShortName(),
                'classNamespace' => $parent->getNamespaceName(),
            );
        }

        return $extends;
    }

    /**
     * Gets an array of classes that the target class implements, if any
     *
     * @param   \ReflectionClass $reflectionClass ReflectionClass instance of target class
     * @return  array
     */
    private function getImplementsClasses(\ReflectionClass $reflectionClass)
    {
        $results = [];
        $interfaces = $reflectionClass->getInterfaces();
        if (!empty($interfaces)) {
            foreach ($interfaces as $interface) {
                $results[] = array(
                    'className'      => $interface->getShortName(),
                    'classNamespace' => $interface->getNamespaceName(),
                );
            }
        }

        return $results;
    }
}

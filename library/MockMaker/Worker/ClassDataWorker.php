<?php

/**
 * ClassDataWorker
 *
 * Analyzes a target class for data needed in mock files.
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerFileData;
use MockMaker\Model\ClassData;
use MockMaker\Worker\TokenWorker;
use MockMaker\Worker\PropertyDataWorker;
use MockMaker\Worker\MethodDataWorker;
use MockMaker\Worker\StringFormatterWorker;
use MockMaker\Helper\TestHelper;

class ClassDataWorker
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
     * Creates a new instance of ClassDataWorker
     *
     * @return  ClassDataWorker
     */
    public function __construct()
    {
        $this->tokenWorker = new TokenWorker();
        $this->propertyWorker = new PropertyDataWorker();
        $this->methodWorker = new MethodDataWorker();
    }

    /**
     * Creates a new ClassData object
     *
     * This will have LOTS of problems if a class doesn't dhere
     * to PSR-0 or PSR-4 class naming standards.
     *
     * @param   MockMakerFileData $fileObj MockMakerFileData object generated by the MockMakerFileDataWorker
     * @return  ClassData
     */
    public function generateNewObject(MockMakerFileData $fileObj)
    {
        $className = $this->determineClassName($fileObj);
        $classNamespace = $this->getClassNamespace($fileObj);
        $reflectionClass = $this->getReflectionClassInstance("{$classNamespace}\\{$className}");
        $classType = $this->getClassType($reflectionClass);

        $obj = new ClassData();
        $obj->setClassName($className)
            ->setClassNamespace($classNamespace)
            ->setReflectionClass($reflectionClass)
            ->setClassType($classType);

        // We cannot mock abstract or interface classes
        if (in_array($classType, array('abstract', 'interface'))) {
            return $obj;
        }

        $methods = $this->getClassMethods($reflectionClass);
        $properties = $this->getClassProperties($reflectionClass, $methods);
        $obj->setHasConstructor($this->getIfClassHasConstructor($reflectionClass))
            ->addUseStatements($this->getClassUseStatements($fileObj->getSourceFileFullPath()))
            ->setExtends($this->getExtendsClass($reflectionClass))
            ->addImplements($this->getImplementsClasses($reflectionClass))
            ->addMethods($methods)
            ->addProperties($properties);

        return $obj;
    }

    /**
     * Determines the class's name
     *
     * @param   MockMakerFileData $fileObj MockMakerFileData object generated by the MockMakerFileDataWorker
     * @return  string
     */
    private function determineClassName(MockMakerFileData $fileObj)
    {
        return str_replace('.php', '', $fileObj->getSourceFileName());
    }

    /**
     * Gets the class's name space
     *
     * @param   MockMakerFileData $fileObj MockMakerFileData object generated by the MockMakerFileDataWorker
     * @return  string
     * @throws  \Exception
     */
    private function getClassNamespace(MockMakerFileData $fileObj)
    {
        $className = $this->determineClassName($fileObj);
        if ($result = $this->checkClassUsingValidNamespacesArray($className)) {
            return $result;
        }
        // not already in our array, so we have to find it the hard way
        $classPath = $this->convertFileNameToClassPath($fileObj);
        if (!$result = $this->getClassNamespaceFromFilePath($classPath)) {
            $msg = "Can't autoload OR non-instantiatable class: {$className}\n";
            $msg .= "Full path: {$fileObj->getSourceFileFullPath()}\n";
            throw new \Exception($msg);
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
     * Converts a fully qualified file path to a namespace formatted string
     *
     * @param   MockMakerFileData $fileObj MockMakerFileData object generated by the MockMakerFileDataWorker
     * @return  string
     */
    private function convertFileNameToClassPath(MockMakerFileData $fileObj)
    {
        // simplify things by removing the root dir path from the file name
        $shortPath = str_replace($fileObj->getProjectRootPath(), '', $fileObj->getSourceFileFullPath());
        // remove extension and beginning / if present
        $basePath = ltrim(str_replace('.php', '', $shortPath), '/');
        // attempt to resolve PSR-0 and PSR-4 namespaces
        $classPath = str_replace(array(DIRECTORY_SEPARATOR, '_'), '\\', $basePath);

        return $classPath;
    }

    /**
     * Attempts to use the class path string to find the qualified class name
     *
     * @param    string $classPath File path converted to class path format
     * @return    mixed
     */
    private function getClassNamespaceFromFilePath($classPath)
    {
        if (!class_exists($classPath)) {
            if (($pos = strpos($classPath, '\\')) === false) {
                return false;
            }

            return $this->getClassNamespaceFromFilePath(substr($classPath, ($pos + 1)));
        }
        if (($lastSlashPos = strrpos($classPath, '\\')) !== false) {
            $classPath = substr($classPath, 0, $lastSlashPos);
        }

        return $classPath;
    }

    /**
     * Creates a ReflectionClass instance of the target class
     *
     * TODO:
     * Eventually this needs to be able to handle classes with constructors as well.
     *
     * Might need to use Mockery to generate mocked versions of the constructor arguments
     * so this can continue.
     *
     * PHP >= 5.4 has the ReflectionClass::newInstanceWithoutConstructor method
     *
     * Otherwise ... parse the file directly?
     *
     * @param   $class  string
     * @return  \ReflectionClass
     */
    private function getReflectionClassInstance($class)
    {
        return new \ReflectionClass($class);
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
    private function getIfClassHasConstructor(\ReflectionClass $class)
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
        if (!empty($interfaces = $reflectionClass->getInterfaces())) {
            foreach ($interfaces as $interface) {
                $results[] = array(
                    'className'      => $interface->getShortName(),
                    'classNamespace' => $interface->getNamespaceName(),
                );
            }
        }

        return $results;
    }

    /**
     * Gets an array of MethodData objects
     *
     * @param   \ReflectionClass $class ReflectionClass instance of target class
     * @return  array
     */
    private function getClassMethods(\ReflectionClass $class)
    {
        return $this->methodWorker->generateMethodObjects($class);
    }

    /**
     * Gets an array of PropertyData objects
     *
     * @param   \ReflectionClass $class   ReflectionClass instance of target class
     * @param   array            $methods Array of MethodData objects
     * @return  array
     */
    private function getClassProperties(\ReflectionClass $class, $methods)
    {
        return $this->propertyWorker->generatePropertyObjects($class, $methods);
    }
}

<?php

/**
 * 	ClassDataWorker
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\FileData;
use MockMaker\Model\ClassData;
use MockMaker\Worker\TokenWorker;
use MockMaker\Worker\PropertyDataWorker;
use MockMaker\Worker\MethodDataWorker;
use MockMaker\Helper\TestHelper;

class ClassDataWorker
{

    /**
     * Array of valid namespaces found during processing.
     *
     * @var array
     */
    private $validNamespaces = [ ];

    /**
     * File token worker class.
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
     * Class that generates PropertyData objects.
     *
     * @var PropertyDataWorker
     */
    private $propertyWorker;

    /**
     * Get the array of valid namespaces.
     *
     * @return  array
     */
    public function getValidNamespaces()
    {
        return $this->validNamespaces;
    }

    /**
     * Add a single or array of namespaces to the validNamespaces array.
     *
     * @param   $validNamespaces    mixed
     */
    public function addValidNamespaces($validNamespaces)
    {
        $namespaces = (is_array($validNamespaces)) ? $validNamespaces : array( $validNamespaces );
        foreach ($namespaces as $namespace) {
            if (!in_array($namespace, $this->validNamespaces)) {
                array_push($this->validNamespaces, $namespace);
            }
        }
    }

    /**
     * Create a new instance of ClassDataWorker
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
     * Create a new ClassData object.
     *
     * This will have LOTS of problems if you don't adhere to PSR-0 or PSR-4 class naming standards.
     *
     * @param   $fileObj    FileData
     * @return  ClassData
     */
    public function generateNewObject(FileData $fileObj)
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
        if (in_array($classType, array( 'abstract', 'interface' ))) {
            return $obj;
        }

        $obj->setHasConstructor($this->getIfClassHasConstructor($reflectionClass))
            ->addUseStatements($this->getClassUseStatements($fileObj->getFullFilePath()))
            ->setExtends($this->getExtendsClass($reflectionClass))
            ->addImplements($this->getImplementsClasses($reflectionClass));
        $methods = $this->getClassMethods($reflectionClass);
        $obj->addMethods($methods);
        $properties = $this->getClassProperties($reflectionClass, $methods);
        $obj->addProperties($properties);

        /*
          $obj->setClassName($this->determineClassName($fileObj))
          ->setClassNamespace($this->getClassNamespace($fileObj));
         */
        return $obj;
    }

    /**
     * Determine the class's name.
     *
     * @param   $fileObj  FileData
     * @return  string
     */
    private function determineClassName(FileData $fileObj)
    {
        return rtrim($fileObj->getFileName(), '.php');
    }

    /**
     * Get the class's name space.
     *
     * @param   $fileObj    FileData
     * @return  string
     */
    private function getClassNamespace(FileData $fileObj)
    {
        $className = $this->determineClassName($fileObj);
        if ($result = $this->checkClassUsingValidNamespacesArray($className)) {
            return $result;
        }
        // not already in our array, so we have to find it the hard way
        $classPath = $this->convertFileNameToClassPath($fileObj);
        if (!$result = $this->getClassNamespaceFromFilePath($classPath)) {
            $msg = "Unable to find a class namespace for {$className}\n";
            $msg .= "File Object Values:\n";
            $msg .= "Full path: {$fileObj->getFullFilePath()}\n";
            $msg .= "File name: {$fileObj->getFileName()}\n";
            throw new \Exception($msg);
        }

        $this->addValidNamespaces($result);
        return $result;
    }

    /**
     * Iterate through the array of currently known valid namespaces for the class.
     *
     * @param   $className  string
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
     * Convert a fully qualified file path to a usable namespace formatted string.
     *
     * @param   $fileObj    FileData
     * @return  string
     */
    private function convertFileNameToClassPath(FileData $fileObj)
    {
        // simplify things by removing the root dir path from the file name
        $shortPath = str_replace($fileObj->getProjectRootPath(), '', $fileObj->getFullFilePath());
        // remove extension and beginning / if present
        $basePath = ltrim(rtrim($shortPath, '.php'), '/');
        // attempt to resolve PSR-0 and PSR-4 namespaces
        $classPath = str_replace(array( DIRECTORY_SEPARATOR, '_' ), '\\', $basePath);

        return $classPath;
    }

    /**
     * Recursively iterate over the file path to find the qualified class name.
     *
     * @param	$filePath	string
     * @return	mixed
     */
    private function getClassNamespaceFromFilePath($filePath)
    {
        if (!class_exists($filePath)) {
            if (( $pos = strpos($filePath, '\\') ) === false) {
                return false;
            }
            return $this->getClassNamespaceFromFilePath(substr($filePath, ($pos + 1)));
        }
        if (($lastSlashPos = strrpos($filePath, '\\')) !== false) {
            $filePath = substr($filePath, 0, $lastSlashPos);
        }

        return $filePath;
    }

    /**
     * Get a reflection class instance of the target class.
     *
     * TODO:
     * Eventually this needs to be able to handle classes with constructors as well.
     *
     * Might need to use Mockery to generate mocked versions of the constructor arguments
     * so this can continue.
     *
     * PHP >= 5.4 has the ReflectionClass::newInstanceWithoutConstructor method.
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
     * Get the class's type - concrete/abstract/interface/final.
     *
     * @param   $class  \ReflectionClass
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
     * Determine of the class has a constructor or not.
     *
     * @param   $class  \ReflectionClass
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
     * Get any use statements in the file.
     *
     * @param   $file   string
     * @return  array
     */
    private function getClassUseStatements($file)
    {
        return $this->tokenWorker->getUseStatementsWithTokens($file);
    }

    /**
     * Get information on the extended class for the target class, if any.
     *
     * @param   $reflectionClass    \ReflectionClass
     * @return  array
     */
    private function getExtendsClass(\ReflectionClass $reflectionClass)
    {
        $extends = [ ];
        if ($parent = $reflectionClass->getParentClass()) {
            $extends = array(
                'className' => $parent->getShortName(),
                'classNamespace' => $parent->getNamespaceName(),
            );
        }

        return $extends;
    }

    /**
     * Get an array of classes that the target class implements, if any.
     *
     * @param   $reflectionClass    \ReflectionClass
     * @return  array
     */
    private function getImplementsClasses(\ReflectionClass $reflectionClass)
    {
        $results = [ ];
        if (!empty($interfaces = $reflectionClass->getInterfaces())) {
            foreach ($interfaces as $interface) {
                $results[] = array(
                    'className' => $interface->getShortName(),
                    'classNamespace' => $interface->getNamespaceName(),
                );
            }
        }

        return $results;
    }

    /**
     * Get an array of MethodData objects.
     *
     * @param   $class  \ReflectionClass
     * @return  array
     */
    private function getClassMethods(\ReflectionClass $class)
    {
        return $this->methodWorker->generateMethodObjects($class);
    }

    /**
     * Get an array of PropertyData objects.
     *
     * @param   $class      \ReflectionClass    Reflection of class to be mocked.
     * @param   $methods    array               Array of MethodData objects.
     * @return  array
     */
    private function getClassProperties(\ReflectionClass $class, $methods)
    {
        return $this->propertyWorker->generatePropertyObjects($class, $methods);
    }

}

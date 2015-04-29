<?php

/**
 * 	MockMakerClassWorker
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerFile;
use MockMaker\Model\MockMakerClass;
use MockMaker\Worker\TokenWorker;

class MockMakerClassWorker
{

    /**
     * Array of valid namespaces found during processing.
     *
     * @var array
     */
    private $validNamespaces = [ ];

    /**
     * File token worker class
     *
     * @var TokenWorker
     */
    private $tokenWorker;

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

    public function __construct()
    {
        $this->tokenWorker = new TokenWorker();
    }

    /**
     * Create a new MockMakerClass object.
     *
     * This will have LOTS of problems if you don't adhere to PSR-0 or PSR-4 class naming standards.
     *
     * @param   $fileObj    MockMakerFile
     * @return  MockMakerClass
     */
    public function generateNewObject(MockMakerFile $fileObj)
    {

        $className = $this->determineClassName($fileObj);
        $classNamespace = $this->getClassNamespace($fileObj);
        $reflectionClass = $this->getReflectionClassInstance("{$classNamespace}\\{$className}");

        $obj = new MockMakerClass();
        $obj->setClassName($className)
            ->setClassNamespace($classNamespace)
            ->setReflectionClass($reflectionClass)
            ->setClassType($this->getClassType($reflectionClass))
            ->setHasConstructor($this->getIfClassHasConstructor($reflectionClass))
            ->addUseStatements($this->getClassUseStatements($fileObj->getFullFilePath()));

        /*
          $obj->setClassName($this->determineClassName($fileObj))
          ->setClassNamespace($this->getClassNamespace($fileObj));
         */
        return $obj;
    }

    /**
     * Determine the class's name.
     *
     * @param   $fileObj  MockMakerFile
     * @return  string
     */
    private function determineClassName(MockMakerFile $fileObj)
    {
        return rtrim($fileObj->getFileName(), '.php');
    }

    /**
     * Get the class's name space.
     *
     * @param   $fileObj    MockMakerFile
     * @return  string
     */
    private function getClassNamespace(MockMakerFile $fileObj)
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
     * @param   $fileObj    MockMakerFile
     * @return  string
     */
    private function convertFileNameToClassPath(MockMakerFile $fileObj)
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

}

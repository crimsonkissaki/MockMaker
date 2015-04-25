<?php

/**
 * MockMaker
 *
 * Generates a basic class mock so you don't have to.
 * Good when you just need a class mock set up for testing.
 *
 * @author		Evan Johnson <evan.johnson@rapp.com>
 * @created		Apr 16, 2015
 * @version		1.0
 */

namespace Minion\MockMakerBundle\Library;

use Minion\MockMakerBundle\Library\MockMaker\Formatter\FormatterInterface;
use Minion\MockMakerBundle\Library\MockMaker\Formatter\DefaultFormatter;
use Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerException;
use Minion\MockMakerBundle\Library\MockMaker\Worker;
use Minion\UnitTestBundle\Library\TestMinion;

class MockMaker
{
    /*
     * These are the only properties that should be publicly settable.
     */

    /**
     * Formatter to use for MockMaker
     *
     * @var	FormatterInterface
     */
    protected $formatter;

    /**
     * Namespace to place in the mock code.
     *
     * @var	string
     */
    private $mockNamespace = '';

    /**
     * Class-only set access
     */

    /**
     * Instance of class we're generating a mock for.
     * Used by a few reflection methods.
     *
     * @var	object
     */
    private $classInstance = null;

    /**
     * Fully qualified name of class to be mocked.
     * Namespace + Class name
     *
     * @var	string
     */
    private $classFullName;

    /**
     * Class name of class to be mocked.
     * e.g. 'MySimpleEntity'
     *
     * @var	string
     */
    private $classShortName;

    /**
     * \ReflectionClass instance of class to be mocked.
     *
     * @var	\ReflectionClass
     */
    private $reflectionClass;

    /**
     * Array of class properties
     *
     * Static properties will show up in both
     * static and whichever other visibility scope type
     * they were declared as.
     *
     * @var	array
     */
    private $classProperties = [ ];

    /**
     * Class properties details.
     *
     * Static properties will be mixed in with their
     * respective "scopes" but have the isStatic flag set
     * to true.
     *
     * @var	array
     */
    private $classPropertyDetails = [ ];

    /**
     * Worker class to process properties
     *
     * @var	Worker\PropertyWorker
     */
    private $propertyWorker;

    /**
     * Array of class methods.
     *
     * Static methods will show up in both
     * static and whichever other visibility scope type
     * they were declared as.
     *
     * @var	array
     */
    private $classMethods = [ ];

    /**
     * Class methods details.
     *
     * @var array
     */
    private $classMethodDetails = [ ];

    /**
     * Worker class to process methods
     *
     * @var	Worker\MethodWorker
     */
    private $methodWorker;

    /**
     * Flag to ensure correct processing.
     *
     * @var	bool
     */
    private $readyToMock = false;

    /**
     * PHP code used to create a mock object.
     *
     * @var	string
     */
    private $mockCode = false;

    /**
     * Set the formatter used for generating PHP mock code.
     *
     * @param	$formatter	FormatterInterface
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Set the namespace to use in the mock's code.
     *
     * If using MockMaker by itself, set this manually.
     * If using the FileGenerator, it will be set on a per-class basis.
     *
     * @param	$mockNamespace	string
     */
    public function setMockNamespace($mockNamespace)
    {
        $this->mockNamespace = $mockNamespace;
    }

    /**
     * Get the class instance.
     *
     * @return	object
     */
    public function getClassInstance()
    {
        return $this->classInstance;
    }

    /**
     * Get the fully qualified name of the class (Namespace + Classname)
     *
     * @return	string
     */
    public function getClassFullName()
    {
        return $this->classFullName;
    }

    /**
     * Get the name of the class (Classname)
     *
     * @return	string
     */
    public function getClassShortName()
    {
        return $this->classShortName;
    }

    /**
     * Get the \ReflectionClass instance of the class to be mocked.
     *
     * @return	\ReflectionClass
     */
    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    /**
     * Get the array of the class's properties.
     *
     * @return	array
     */
    public function getClassProperties()
    {
        return $this->classProperties;
    }

    /**
     * Get the array of full details for the class's properties
     *
     * @return	array
     */
    public function getClassPropertyDetails()
    {
        return $this->classPropertyDetails;
    }

    /**
     * Get the array of the class's methods.
     *
     * @return	array
     */
    public function getClassMethods()
    {
        return $this->classMethods;
    }

    /**
     * Get the array of full details for the class's methods
     *
     * @return type
     */
    public function getClassMethodDetails()
    {
        return $this->classMethodDetails;
    }

    /**
     * Get the formatter used for generating PHP mock code.
     *
     * @return	FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Get the generated mock code.
     *
     * @return type
     */
    public function getMockCode()
    {
        return $this->mockCode;
    }

    /**
     * Get the namespace to use in the mock's code.
     *
     * @return	string
     */
    public function getMockNamespace()
    {
        return $this->mockNamespace;
    }

    /**
     * Instantiate a new MockMaker instance.
     *
     * If you want/need default values pre-populated
     * you have to pass in a valid class instance,
     * otherwise default values (other than static) cannot be obtained.
     *
     * This constructor does the bare minimum to get you going
     * so you can at least check the MockMaker status afterwards
     * to ensure good class value assignments.
     *
     * @param	$class		mixed					Class to be mocked: instance or fully qualified name.
     * @param	$formatter	FormatterInterface		Formatter class used to generate PHP code output.
     * @throws	InvalidArgumentException
     * @return	MockMaker
     */
    public function __construct($class, FormatterInterface $formatter = null)
    {
        if (!is_object($class) && !class_exists($class, false)) {
            throw new \InvalidArgumentException("MockMaker only accepts instantiated class objects or fully qualified class names.");
        }
        $this->setUpClassValues($class);
        $this->formatter = (is_null($formatter)) ? new DefaultFormatter : $formatter;

        return $this;
    }

    /**
     * Gather all the data!
     *
     * @throws	\InvalidArgumentException
     * @return	bool
     */
    public function analyzeClass()
    {
        try {
            $this->setUpReflectionClass($this->classFullName);
            $this->methodWorker = new Worker\MethodWorker;
            $this->getAllClassMethods();
            $this->getAllClassMethodDetails();

            $this->propertyWorker = new Worker\PropertyWorker($this->classMethods, $this->classInstance, $this->reflectionClass);
            $this->getAllClassProperties();
            $this->getAllClassPropertyDetails();

            $this->generateNamespace();
            $this->readyToMock = true;
        } catch (\Exception $e) {
            TestMinion::dbug($e, "Exception while analyzing:", TRUE);
        }

        return $this->readyToMock;
    }

    /**
     * Get the PHP code for a basic mock.
     *
     * @return	string
     */
    public function getBasicMockCode()
    {
        if (!$this->mockCode) {
            $this->generateBasicMock();
        }

        return $this->mockCode;
    }

    /**
     * Generate a basic mockup of a class.
     *
     * @throws	\Exception
     * @return	array
     */
    private function generateBasicMock()
    {
        try {
            if (!$this->readyToMock) {
                if (!$this->analyzeClass()) {
                    throw new MockMakerException(MockMakerException::CLASS_CANNOT_BE_MOCKED);
                }
            }
            $this->mockCode = $this->getFormatter()->generateMockCode($this);
        } catch (\Exception $e) {
            TestMinion::dbug($e->getTraceAsString(), "Exception:");
        }

        return $this->mockCode;
    }

    /**
     * Set up various class values we'll need to generate the mock
     *
     * @param	$class	mixed
     */
    private function setUpClassValues($class)
    {
        $this->classFullName = (is_object($class)) ? get_class($class) : $class;
        $this->classShortName = join('', array_slice(explode('\\', $this->classFullName), -1));
        $this->classInstance = (is_object($class)) ? $class : new $class;
    }

    /**
     * Create a \ReflectionClass object from the class.
     *
     * @param	$classFullName	string
     */
    private function setUpReflectionClass($classFullName)
    {
        $this->reflectionClass = new \ReflectionClass($classFullName);
    }

    /**
     * Get all of the class's properties.
     */
    private function getAllClassProperties()
    {
        $this->classProperties['constant'] = $this->reflectionClass->getConstants();
        $this->classProperties['public'] = $this->reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        $this->classProperties['protected'] = $this->reflectionClass->getProperties(\ReflectionProperty::IS_PROTECTED);
        $this->classProperties['private'] = $this->reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);
        $this->classProperties['static'] = $this->reflectionClass->getProperties(\ReflectionProperty::IS_STATIC);
    }

    /**
     * Get details for all class properties.
     */
    private function getAllClassPropertyDetails()
    {
        $this->classPropertyDetails = $this->propertyWorker->getAllClassPropertyDetails($this->classProperties);
    }

    /**
     * Get all of the class's methods
     */
    private function getAllClassMethods()
    {
        $this->classMethods['abstract'] = $this->reflectionClass->getMethods(\ReflectionMethod::IS_ABSTRACT);
        $this->classMethods['final'] = $this->reflectionClass->getMethods(\ReflectionMethod::IS_FINAL);
        $this->classMethods['public'] = $this->reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $this->classMethods['protected'] = $this->reflectionClass->getMethods(\ReflectionMethod::IS_PROTECTED);
        $this->classMethods['private'] = $this->reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE);
        $this->classMethods['static'] = $this->reflectionClass->getMethods(\ReflectionMethod::IS_STATIC);
    }

    /**
     * Get all of the class's method's details.
     */
    private function getAllClassMethodDetails()
    {
        $this->classMethodDetails = $this->methodWorker->getAllClassMethodDetails($this->classMethods);
    }

    /**
     * Best guess attempt at a namespace for the mock file if none is specified.
     *
     * @return	bool
     */
    private function generateNamespace()
    {
        if (!$this->getMockNamespace()) {
            $this->setMockNamespace($this->getReflectionClass()->getNamespaceName());

            return TRUE;
        }

        return FALSE;
    }

}

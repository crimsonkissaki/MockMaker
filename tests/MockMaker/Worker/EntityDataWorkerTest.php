<?php

/**
 * 	EntityDataWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\EntityDataWorker;
use MockMaker\Worker\DataContainerWorker;
use MockMaker\Model\DataContainer;
use MockMaker\Model\ConfigData;
use MockMaker\Model\EntityData;
use MockMaker\TestHelper\TestHelper;
use MockMaker\Entities;

class EntityDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    /* @var $worker EntityDataWorker */
    public $worker;
    /* @var $fileObj DataContainer */
    public $fileObj;
    /* @var $config ConfigData */
    public $config;
    /* @var $classData EntityData */
    public $classData;
    public $fileName;
    public $rootDir;

    public function setUp()
    {
        $this->rootDir = dirname(dirname(dirname(dirname(__FILE__)))).'/';
        $this->fileName = $this->rootDir . 'tests/MockMaker/Entities/SimpleEntity.php';

        $this->worker = new EntityDataWorker();
        $this->config = new ConfigData();
        $this->config->setProjectRootPath($this->rootDir);
        //$fileWorker = new DataContainerWorker();
        //$this->fileObj = $fileWorker->createFileDataObject($this->fileName, $this->config);
        $this->classData = new EntityData();
    }

    public function test_addValidNamespaces_addsNewNamespaceToArray()
    {
        $namespace = 'MockMaker\Worker';
        $expected = array(
            $namespace,
        );
        $this->worker->addValidNamespaces($namespace);
        $this->assertEquals($expected, $this->worker->getValidNamespaces());
    }

    public function test_addValidNamespaces_doesNotAddExistingNamespaceToArray()
    {
        $namespace = 'MockMaker\Worker';
        $expected = array(
            $namespace,
        );
        $this->worker->addValidNamespaces($namespace);
        $this->worker->addValidNamespaces($namespace);
        $this->assertEquals($expected, $this->worker->getValidNamespaces());
    }

    public function test_determineClassNamespace_returnsCorrectNamespace()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineClassNamespace');
        $actual = $method->invoke($this->worker, $this->fileName, $this->rootDir);
        $expected = 'MockMaker\Entities';
        $this->assertEquals($expected, $actual);
    }

    public function test_checkClassUsingValidNamespacesArray_returnsTrueIfValidNamespaceIsInArray()
    {
        $className = 'TestEntity';
        $expected = 'MockMaker\Entities';
        $this->worker->addValidNamespaces($expected);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'checkClassUsingValidNamespacesArray');
        $actual = $method->invoke($this->worker, $className);
        $this->assertEquals($expected, $actual);
    }

    public function test_checkClassUsingValidNamespacesArray_returnsFalseIfNoElementsInValidNamespaceArray()
    {
        $className = 'TestEntity';
        $expected = false;
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'checkClassUsingValidNamespacesArray');
        $actual = $method->invoke($this->worker, $className);
        $this->assertEquals($expected, $actual);
    }

    public function test_checkClassUsingValidNamespacesArray_returnsFalseIfInvalidClassname()
    {
        $className = 'InvalidEntity';
        $expected = false;
        $this->worker->addValidNamespaces('MockMaker\Entities');
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'checkClassUsingValidNamespacesArray');
        $actual = $method->invoke($this->worker, $className);
        $this->assertEquals($expected, $actual);
    }

    public function getClassNamespaceFromFilePathProvider()
    {
        return array(
            array( false, 'InvalidClass' ),
            array( false, 'InvalidPath\To\InvalidClass' ),
            array( '', '\stdClass' ),
            array( '', 'stdClass' ),
            array( 'MockMaker\Entities', 'MockMaker\Entities\SimpleEntity' ),
        );
    }

    /**
     * @dataProvider getClassNamespaceFromFilePathProvider
     */
    public function test_getClassNamespaceFromFilePath_($expected, $classPath)
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getClassNamespaceFromFilePath');
        $actual = $method->invoke($this->worker, $classPath);
        $this->assertEquals($expected, $actual);
    }

    public function test_createReflectionClass_returnsReflectionClass()
    {
        $this->classData->setClassName('SimpleEntity');
        $this->classData->setClassNamespace('MockMaker\Entities');
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'createReflectionClass');
        $actual = $method->invoke($this->worker, $this->classData);
        $this->assertInstanceOf('\ReflectionClass', $actual);
    }

    public function getClassTypeProvider()
    {
        return array(
            array( 'concrete', 'MockMaker\Entities\SimpleEntity' ),
            array( 'concrete', 'MockMaker\Entities\EntityThatExtends' ),
            array( 'abstract', 'MockMaker\Entities\AbstractEntity' ),
        );
    }

    /**
     * @dataProvider getClassTypeProvider
     */
    public function test_getClassType_returnsCorrectClassType($expected, $class)
    {
        $reflection = new \ReflectionClass($class);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getClassType');
        $actual = $method->invoke($this->worker, $reflection);
        $this->assertEquals($expected, $actual);
    }

    public function generateNewClassObjectExceptionProvider()
    {
        return array(
            array( $this->rootDir.'tests/MockMaker/Entities/AbstractEntity' ),
            array( $this->rootDir.'tests/MockMaker/Entities/EntityInterface' ),
        );
    }

    /**
     * @dataProvider generateNewClassObjectExceptionProvider
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_generateNewClassObject_throwsExceptionForInvalidClasses($file)
    {
        $actual = $this->worker->generateEntityDataObject($file, $this->config);
    }

   public function test_getClassUseStatements_returnsCorrectUseStatements()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getClassUseStatements');
        $actual = $method->invoke($this->worker, $this->fileName);
        $expected = array(
            'use MockMaker\Entities\TestEntity;',
            'use MockMaker\Entities\PropertyWorkerEntity;',
        );
        $this->assertEquals($expected, $actual);
    }

    public function test_getExtendsClass_returnsCorrectData()
    {
        $reflection = new \ReflectionClass('MockMaker\Entities\EntityThatExtends');
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getExtendsClass');
        $actual = $method->invoke($this->worker, $reflection);
        $expected = array(
            'className' => 'AbstractEntity',
            'classNamespace' => 'MockMaker\Entities',
        );
        $this->assertEquals($expected, $actual);
    }

    public function test_getImplementsClasses_returnsCorrectData()
    {
        $reflection = new \ReflectionClass('MockMaker\Entities\EntityThatImplements');
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getImplementsClasses');
        $actual = $method->invoke($this->worker, $reflection);
        $expected = array( array(
                'className' => 'EntityInterface',
                'classNamespace' => 'MockMaker\Entities'
            ) );
        $this->assertEquals($expected, $actual);
    }

    public function test_getImplementsClasses_returnsEmptyArrayIfNoInterfaces()
    {
        $reflection = new \ReflectionClass('MockMaker\Entities\SimpleEntity');
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getImplementsClasses');
        $actual = $method->invoke($this->worker, $reflection);
        $this->assertEquals([ ], $actual);
    }

}

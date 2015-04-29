<?php

/**
 * 	MockMakerClassWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\MockMakerClassWorker;
use MockMaker\Worker\MockMakerFileWorker;
use MockMaker\Model\MockMakerFile;
use MockMaker\Model\MockMakerConfig;
use MockMaker\Helper\TestHelper;
use MockMaker\Entities;

class MockMakerClassWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker MockMakerClassWorker
    public $worker;
    // @var $fileObj MockMakerFile
    public $fileObj;
    // @var $config MockMakerConfig
    public $config;
    public $fileName = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->config = new MockMakerConfig();
        $this->config->setProjectRootPath('/Applications/XAMPP/xamppfiles/htdocs/mockmaker/');
        $fileWorker = new MockMakerFileWorker();
        $this->fileObj = $fileWorker->generateNewObject($this->fileName, $this->config);
        $this->worker = new MockMakerClassWorker();
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

    public function test_determineClassName_returnsCorrectClassName()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineClassName');
        $actual = $method->invoke($this->worker, $this->fileObj);
        $this->assertEquals('SimpleEntity', $actual);
    }

    public function test_convertFileNameToClassPath_returnsCorrectClassPath()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'convertFileNameToClassPath');
        $actual = $method->invoke($this->worker, $this->fileObj);
        $this->assertEquals('tests\MockMaker\Entities\SimpleEntity', $actual);
    }

    public function test_getClassNamespace_returnsCorrectNamespace()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getClassNamespace');
        $actual = $method->invoke($this->worker, $this->fileObj);
        $expected = 'MockMaker\Entities';
        $this->assertEquals($expected, $actual);
    }

    public function test_getClassNamespaceFromFilePath_returnsCorrectNamespace()
    {
        $filePath = 'tests\MockMaker\Entities\SimpleEntity';
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getClassNamespaceFromFilePath');
        $actual = $method->invoke($this->worker, $filePath);
        $expected = 'MockMaker\Entities';
        $this->assertEquals($expected, $actual);
    }

    public function test_getReflectionClassInstance_returnsReflectionClass()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getReflectionClassInstance');
        $actual = $method->invoke($this->worker, 'MockMaker\Entities\SimpleEntity');
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

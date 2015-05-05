<?php

/**
 * 	ArgumentDataWorkerTest
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 29, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\ArgumentDataWorker;
use MockMaker\Helper\TestHelper;
use MockMaker\Entities\TestEntity;
use MockMaker\Entities\MethodWorkerEntity;

class ArgumentDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker ArgumentDataWorker
    public $worker;
    // @var $argument \ReflectionParameter
    public $argument;

    public function setUp()
    {
        $this->worker = new ArgumentDataWorker();
        $method = new \ReflectionMethod('MockMaker\Entities\TestEntity', 'setPublicTypehintedProperty1');
        $arguments = $method->getParameters();
        $this->argument = $arguments[0];
    }

    public function _test_generateArgumentObjects()
    {
        $this->assertTrue(false);
    }

    public function test_getArgumentDetails()
    {
        $method = new \ReflectionMethod('MockMaker\Entities\MethodWorkerEntity', 'twoArgumentsOneOptional');
        $arguments = $method->getParameters();
        $testedMethod = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getArgumentDetails');

        $actual0 = $testedMethod->invoke($this->worker, $arguments[0]);
        $argResult = $actual0;
        $this->assertEquals('argument1', $argResult->name);
        $this->assertEmpty($argResult->dataType);
        $this->assertTrue($argResult->isRequired);
        $this->assertTrue($argResult->allowsNull);
        $this->assertEmpty($argResult->defaultValue);
        $this->assertEmpty($argResult->className);
        $this->assertEmpty($argResult->classNamespace);
        $this->assertEmpty($argResult->passedByReference);

        $actual1 = $testedMethod->invoke($this->worker, $arguments[1]);
        $argResult = $actual1;
        $this->assertEquals('argument2', $argResult->name);
        $this->assertEquals('string', $argResult->dataType);
        $this->assertFalse($argResult->isRequired);
        $this->assertTrue($argResult->allowsNull);
        $this->assertEquals('defaultArgument2Value', $argResult->defaultValue);
        $this->assertEmpty($argResult->className);
        $this->assertEmpty($argResult->classNamespace);
        $this->assertEmpty($argResult->passedByReference);
    }

    public function getArgumentTypeProvider()
    {
        return array(
            array( 'twoArgumentsTypehinted', '0', 'object' ),
            array( 'twoArgumentsTypehinted', '1', 'object' ),
            array( 'twoArgumentsOneTypehintedOneDefaultNull', '0', 'object' ),
            array( 'twoArgumentsOneTypehintedOneDefaultNull', '1', 'NULL' ),
            array( 'oneArgumentDefaultString', '0', 'string' ),
            array( 'oneArgumentDefaultBoolTrue', '0', 'boolean' ),
            array( 'oneArgumentDefaultBoolFalse', '0', 'boolean' ),
            array( 'oneArgumentDefaultInteger', '0', 'integer' ),
            array( 'oneArgumentTypehintedWithDefaultNull', '0', 'object' ),
            array( 'oneArgumentDefaultArray', '0', 'array' ),
        );
    }

    /**
     * @dataProvider getArgumentTypeProvider
     */
    public function test_getArgumentType($methodName, $argNo, $expected)
    {
        $method = new \ReflectionMethod('MockMaker\Entities\MethodWorkerEntity', $methodName);
        $arguments = $method->getParameters();
        $argument = $arguments[$argNo];
        $testedMethod = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getArgumentType');
        $actual = $testedMethod->invoke($this->worker, $argument);
        $this->assertEquals($expected, $actual);
    }

    public function test_getDefaultValueClassData_returnsCorrectClassDataForTopLevelClass()
    {
        $string = 'Parameter #0 [ <required> DateTime $simpleEntity ]';
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getDefaultValueClassData');
        $actual = $method->invoke($this->worker, $string);
        $expected = array(
            'className' => 'DateTime',
            'classNamespace' => ''
        );
        $this->assertEquals($expected, $actual);
    }

    public function test_getDefaultValueClassData_returnsCorrectClassDataForUserDefinedClass()
    {
        $string = 'Parameter #0 [ <required> MockMaker\Entities\SimpleEntity $simpleEntity ]';
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getDefaultValueClassData');
        $actual = $method->invoke($this->worker, $string);
        $expected = array(
            'className' => 'SimpleEntity',
            'classNamespace' => 'MockMaker\Entities'
        );
        $this->assertEquals($expected, $actual);
    }

}

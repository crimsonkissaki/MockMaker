<?php

/**
 *	ArgumentWorkerTest
 *
 *	No point in testing private setters/getters, as they can't be overridden.
 *	Public/protected need to be pulled over so we can set them.
 *
 *	This is seriously going to violate the usual "one test one assertion"
 *	SOP, but what can you do?
 *
 *	@author		Evan Johnson
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Library\MockMaker\Worker;

use Minion\MockMakerBundle\Library\MockMaker\Worker\ArgumentWorker;
use Minion\MockMakerBundle\Tests\Resources\Entities\MethodWorkerEntity;
use Minion\MockMakerBundle\Tests\Resources\Entities\SimpleEntity;

use Minion\MockMakerBundle\Library\MockMaker\Model\MethodDetails;
use Minion\MockMakerBundle\Library\MockMaker\Model\ArgumentDetails;

use Minion\UnitTestBundle\Library\DebuggerMinion;
use Minion\UnitTestBundle\Library\ReflectionMinion;

class ArgumentWorkerTest extends \PHPUnit_Framework_TestCase
{

	/* @var ArgumentWorker */
	public $worker;
	/* @var MethodWorkerEntity */
	public $entity;

	public function setUp()
	{
		$this->worker = new ArgumentWorker;
		$this->entity = new MethodWorkerEntity;
	}

	/**
	 * Testing getAllMethodArgumentsDetails with various methods
	 */
		//DebuggerMinion::dbug( $actual, "result from worker", TRUE );

	public function test_publicMethodWithNoArguments()
	{
		$method = new \ReflectionMethod( $this->entity, 'noArguments' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );

		$this->assertEmpty( $actual );
	}

	public function test_setSomeProperty()
	{
		$method = new \ReflectionMethod( $this->entity, 'setSomeProperty' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );

		// @var $argResult ArgumentDetails
		$argResult = $actual[0];
		$this->assertEquals( 'setSomePropertyArgument', $argResult->name );
		$this->assertEmpty( $argResult->type );
		$this->assertTrue( $argResult->isRequired );
		$this->assertTrue( $argResult->allowsNull );
		$this->assertEmpty( $argResult->defaultValue );
		$this->assertEmpty( $argResult->typeHint );
		$this->assertEmpty( $argResult->passedByReference );
	}

	public function test_oneArgumentNoTypehint()
	{
		$method = new \ReflectionMethod( $this->entity, 'oneArgumentNoTypehint' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );

		$argResult = $actual[0];
		$this->assertEquals( 'oneArgumentNoTypehintArgument', $argResult->name );
		$this->assertEmpty( $argResult->type );
		$this->assertTrue( $argResult->isRequired );
		$this->assertTrue( $argResult->allowsNull );
		$this->assertEmpty( $argResult->defaultValue );
		$this->assertEmpty( $argResult->typeHint );
		$this->assertEmpty( $argResult->passedByReference );
	}

	public function test_twoArgumentsOneOptional()
	{
		$method = new \ReflectionMethod( $this->entity, 'twoArgumentsOneOptional' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );

		$argResult = $actual[0];
		$this->assertEquals( 'argument1', $argResult->name );
		$this->assertEmpty( $argResult->type );
		$this->assertTrue( $argResult->isRequired );
		$this->assertTrue( $argResult->allowsNull );
		$this->assertEmpty( $argResult->defaultValue );
		$this->assertEmpty( $argResult->typeHint );
		$this->assertEmpty( $argResult->passedByReference );
		$argResult = $actual[1];
		$this->assertEquals( 'argument2', $argResult->name );
		$this->assertEquals( 'string', $argResult->type );
		$this->assertFalse( $argResult->isRequired );
		$this->assertTrue( $argResult->allowsNull );
		$this->assertEquals( 'defaultArgument2Value', $argResult->defaultValue );
		$this->assertEmpty( $argResult->typeHint );
		$this->assertEmpty( $argResult->passedByReference );
	}

	public function test_oneArgumentTypehintedException()
	{
		$method = new \ReflectionMethod( $this->entity, 'oneArgumentTypehintedException' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );

		$argResult = $actual[0];
		$this->assertEquals( 'exception', $argResult->name );
		$this->assertEquals( 'object', $argResult->type );
		$this->assertEquals( '\Exception', $argResult->typeHint );
		$this->assertTrue( $argResult->isRequired );
		$this->assertFalse( $argResult->allowsNull );
		$this->assertEmpty( $argResult->defaultValue );
		$this->assertEmpty( $argResult->passedByReference );
	}

	public function test_oneArgumentTypehinted()
	{
		$method = new \ReflectionMethod( $this->entity, 'oneArgumentTypehinted' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );

		$argResult = $actual[0];
		$this->assertEquals( 'simpleEntity', $argResult->name );
		$this->assertEquals( 'object', $argResult->type );
		$this->assertEquals( 'Minion\MockMakerBundle\Tests\Resources\Entities\SimpleEntity', $argResult->typeHint );
		$this->assertTrue( $argResult->isRequired );
		$this->assertFalse( $argResult->allowsNull );
		$this->assertEmpty( $argResult->defaultValue );
		$this->assertEmpty( $argResult->passedByReference );
	}

	public function test_oneArgumentTypehintedWithDefaultNull()
	{
		$method = new \ReflectionMethod( $this->entity, 'oneArgumentTypehintedWithDefaultNull' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );
		//DebuggerMinion::dbug( $actual, "result from worker", TRUE );

		$argResult = $actual[0];
		$this->assertEquals( 'simpleEntity', $argResult->name );
		$this->assertEquals( 'object', $argResult->type );
		$this->assertEquals( 'Minion\MockMakerBundle\Tests\Resources\Entities\SimpleEntity', $argResult->typeHint );
		$this->assertFalse( $argResult->isRequired );
		$this->assertTrue( $argResult->allowsNull );
		$this->assertEmpty( $argResult->defaultValue );
		$this->assertEmpty( $argResult->passedByReference );
	}

	/*
	public function test_()
	{
		$method = new \ReflectionMethod( $this->entity, 'noArguments' );
		$actual = $this->worker->getAllMethodArgumentsDetails( $method );

		$actual = $this->getMethodDetails( 'noArguments', 'public' );
	}
	 *
	( $argument )
	twoArgumentsNoTypehint( $argument1, $argument2 )
	twoArgumentsOneOptional( $argument1, $argument2 = 'defaultArgument2Value' )
	oneArgumentDefaultNull( $argument = null )
	oneArgumentDefaultBoolFalse( $argument = false )
	oneArgumentDefaultBoolTrue( $argument = true )
	oneArgumentDefaultString( $argument = 'argumentDefaultValue' )
	oneArgumentTypehinted( SimpleEntity $simpleEntity )
	twoArgumentsOneTypehintedOneDefaultNull( SimpleEntity $simpleEntity, $argument2 = null )

	protectedNoArguments()
	protectedOneArgumentNoTypehint( $argument )
	protectedTwoArgumentsNoTypehint( $argument1, $argument2 )
	protectedTwoArgumentsOneOptional( $argument1, $argument2 = 'defaultArgument2Value' )
	protectedOneArgumentDefaultNull( $argument = null )
	protectedOneArgumentDefaultBoolFalse( $argument = false )
	protectedOneArgumentDefaultBoolTrue( $argument = true )
	protectedOneArgumentDefaultString( $argument = 'argumentDefaultValue' )
	protectedOneArgumentTypehinted( SimpleEntity $simpleEntity )
	protectedTwoArgumentsOneTypehintedOneDefaultNull( SimpleEntity $simpleEntity, $argument2 = null )
	*/

}

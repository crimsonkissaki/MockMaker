<?php

/**
 *	MethodWorkerTest
 *
 *	No point in testing private setters/getters, as they can't be overridden.
 *	Public/protected need to be pulled over so we can set them.
 *
 *	This is seriously going to violate the usual "one test one assertion"
 *	SOP, but what can you do?
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Library\MockMaker\Worker;

use Minion\MockMakerBundle\Library\MockMaker\Worker\MethodWorker;
use Minion\MockMakerBundle\Library\MockMaker\Worker\ArgumentWorker;
use Minion\MockMakerBundle\Tests\Resources\Entities\MethodWorkerEntity;
use Minion\MockMakerBundle\Tests\Resources\Entities\SimpleEntity;

use Minion\MockMakerBundle\Library\MockMaker\Model\MethodDetails;
use Minion\MockMakerBundle\Library\MockMaker\Model\ArgumentDetails;

use Minion\UnitTestBundle\Library\DebuggerMinion;
use Minion\UnitTestBundle\Library\ReflectionMinion;

class MethodWorkerTest extends \PHPUnit_Framework_TestCase
{

	/* @var MethodWorker */
	public $worker;
	/* @var MethodWorkerEntity */
	public $entity;

	public function setUp()
	{
		$this->worker = new MethodWorker;
		$this->entity = new MethodWorkerEntity;
	}

	/**
	 * Run a method through the worker->getMethodDetails process.
	 *
	 * @param	$methodName		string
	 * @param	$scope			string
	 * @return	MethodDetails
	 */
	public function getMethodDetails( $methodName, $scope )
	{
		// Create a \ReflectionMethod instance of the entity method to run through the test
		$entityMethod = new \ReflectionMethod( $this->entity, $methodName );
		// Make the getMethodDetails() method accessible so it can be tested
		$detailsMethod = ReflectionMinion::getAccessibleNonPublicMethod( $this->worker, 'getMethodDetails' );
		// Run the entity method \ReflectionMethod instance through getMethodDetails() and return the result
		return $detailsMethod->invoke( $this->worker, $scope, $entityMethod );
	}

	/**
	 * Testing getMethodDetails with various methods from the MethodWorkerEntity
	 */
	public function test_publicMethodWithNoArguments()
	{
		$actual = $this->getMethodDetails( 'noArguments', 'public' );
		$this->assertEquals( 'noArguments', $actual->name );
		$this->assertEquals( 'public', $actual->scope );
		$this->assertFalse( $actual->isSetter );
		$this->assertEmpty( $actual->arguments );
	}

	public function test_setSomeProperty()
	{
		$actual = $this->getMethodDetails( 'setSomeProperty', 'public' );
		$this->assertEquals( 'setSomeProperty', $actual->name );
		$this->assertEquals( 'public', $actual->scope );
		$this->assertTrue( $actual->isSetter );
		$this->assertNotEmpty( $actual->arguments );
	}

	public function test_oneArgumentNoTypehint()
	{
		$actual = $this->getMethodDetails( 'oneArgumentNoTypehint', 'public' );
		$this->assertEquals( 'oneArgumentNoTypehint', $actual->name );
		$this->assertEquals( 'public', $actual->scope );
		$this->assertFalse( $actual->isSetter );
		$this->assertNotEmpty( $actual->arguments );
	}

	public function test_twoArgumentsOneOptional()
	{
		$actual = $this->getMethodDetails( 'twoArgumentsOneOptional', 'public' );
		$this->assertEquals( 'twoArgumentsOneOptional', $actual->name );
		$this->assertEquals( 'public', $actual->scope );
		$this->assertFalse( $actual->isSetter );
		$this->assertNotEmpty( $actual->arguments );
	}

	public function test_oneArgumentTypehinted()
	{
		$actual = $this->getMethodDetails( 'oneArgumentTypehinted', 'public' );
		$this->assertEquals( 'public', $actual->scope );
		$this->assertEquals( 'oneArgumentTypehinted', $actual->name );
		$this->assertFalse( $actual->isSetter );
		$this->assertNotEmpty( $actual->arguments );
	}

	public function test_oneArgumentTypehintedWithDefaultNull()
	{
		$actual = $this->getMethodDetails( 'oneArgumentTypehintedWithDefaultNull', 'public' );
		$this->assertEquals( 'public', $actual->scope );
		$this->assertEquals( 'oneArgumentTypehintedWithDefaultNull', $actual->name );
		$this->assertFalse( $actual->isSetter );
		$this->assertNotEmpty( $actual->arguments );
	}

	public function test_oneArgumentTypehintedException()
	{
		$actual = $this->getMethodDetails( 'oneArgumentTypehintedException', 'public' );
		$this->assertEquals( 'public', $actual->scope );
		$this->assertEquals( 'oneArgumentTypehintedException', $actual->name );
		$this->assertFalse( $actual->isSetter );
		$this->assertNotEmpty( $actual->arguments );
	}


	public function test_privateMethodWithNoArguments()
	{
		$actual = $this->getMethodDetails( 'privateNoArguments', 'private' );
		$this->assertEquals( 'privateNoArguments', $actual->name );
		$this->assertEquals( 'private', $actual->scope );
		$this->assertFalse( $actual->isSetter );
		$this->assertEmpty( $actual->arguments );
	}

	public function test_protectedMethodWithNoArguments()
	{
		$actual = $this->getMethodDetails( 'protectedNoArguments', 'protected' );
		$this->assertEquals( 'protectedNoArguments', $actual->name );
		$this->assertEquals( 'protected', $actual->scope );
		$this->assertFalse( $actual->isSetter );
		$this->assertEmpty( $actual->arguments );
	}






}
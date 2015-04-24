<?php

/**
 *	PropertyWorkerTest
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Library\Worker;

use Minion\MockMakerBundle\Library\MockMaker\Worker\PropertyWorker;
use Minion\MockMakerBundle\Tests\Resources\Entities\PropertyWorkerEntity;
use Minion\UnitTestBundle\Library\DebuggerMinion;

class PropertyWorkerTest extends \PHPUnit_Framework_TestCase
{

	public $worker;
	public $entity;
	public $reflection;

	public function setUp()
	{
		$this->entity = new PropertyWorkerEntity;
		$this->reflection = new \ReflectionClass( $this->entity );
		$this->worker = new PropertyWorker( [], $this->entity, $this->reflection );
	}

	public function propertyNumberValidation()
	{
		return array(
			array( 'constant', 4 ),
			array( 'public', 6, \ReflectionProperty::IS_PUBLIC ),
			array( 'private', 6, \ReflectionProperty::IS_PRIVATE ),
			array( 'protected', 6, \ReflectionProperty::IS_PROTECTED ),
		);
	}

	/**
	 * @dataProvider propertyNumberValidation
	 */
	public function test_getAllClassPropertyDetails_returnsProperNumberOfProperties( $scope, $expected, $filter = null )
	{
		$publicProp[$scope] = ($scope === 'constant') ? $this->reflection->getConstants() : $this->reflection->getProperties( $filter );
		$actual = $this->worker->getAllClassPropertyDetails( $publicProp );

		$this->assertEquals( $expected, count($actual[$scope]) );
	}

	public function staticPropertiesProvider()
	{
		return array(
			array( 'public', 2, \ReflectionProperty::IS_PUBLIC ),
			array( 'private', 2, \ReflectionProperty::IS_PUBLIC ),
			array( 'protected', 2, \ReflectionProperty::IS_PUBLIC ),
		);
	}

	/**
	 * @dataProvider staticPropertiesProvider
	 */
	public function test_getAllClassPropertyDetails_returnsCorrectNumberOfStaticProperties( $scope, $expected, $filter )
	{
		$publicProp[$scope] = $this->reflection->getProperties( $filter );
		$publicProp['static'] = $this->reflection->getProperties(\ReflectionProperty::IS_STATIC);
		$actual = $this->worker->getAllClassPropertyDetails( $publicProp );

		$actualCount = 0;
		foreach( $actual[$scope] as $key => $property ) {
			if( $property->isStatic ) {
				$actualCount += 1;
			}
		}
		$this->assertEquals( $expected, $actualCount );
	}

}

<?php

/**
 *	MockMakerTest
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 18, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Library;

use Minion\MockMakerBundle\Library\MockMaker;
use Minion\MockMakerBundle\Tests\Resources\Entities;

use Minion\MockMakerBundle\Library\Model\PropertyDetails;
use Minion\UnitTestBundle\Library\TestMinion;

class MockMakerTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * All of these should result in InvalidArgumentExceptions
	 *
	 * @return	array
	 */
	public function badConstructorArgumentsProvider()
	{
		return array(
			array( 1 ),
			array( false ),
			array( true ),
			array( 'bad string' ),
			array( null ),
			array( 'Minion\MockMakerBundle\Tests\Resources\Entities\InvalidClass' ),
			array( '\Applications\XAMPP\xamppfiles\htdocs\mockMaker\src\Minion\MockMakerBundle\Tests\Resources\Entities\SimpleEntity' ),
		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @dataProvider badConstructorArgumentsProvider
	 */
	public function test_construct_throwsInvalidArgumentException_withInvalidArgument( $argument )
	{
		$actual = new MockMaker( $argument );
	}

	public function test_construct_returnsProperNumberOfProperties_withClassInstanceArgument()
	{
		$testClass = new Entities\TestEntity();
		$actual = new MockMaker( $testClass );
		$actual->analyzeClass();

		$properties = $actual->getClassProperties();

		$this->assertEquals( 4, count($properties['constant']) );
		$this->assertEquals( 8, count($properties['public']) );
		$this->assertEquals( 8, count($properties['private']) );
		$this->assertEquals( 8, count($properties['protected']) );
		$this->assertEquals( 12, count($properties['static']) );
	}

}


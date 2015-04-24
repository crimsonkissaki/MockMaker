<?php

/**
 *	DefaultFormatterTest
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 20, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Library\MockMaker\Formatter;

use Minion\MockMakerBundle\Library\MockMaker\Formatter\DefaultFormatter;
use Minion\UnitTestBundle\Library\ReflectionMinion;
use Minion\UnitTestBundle\Library\DebuggerMinion;

class DefaultFormatterTest extends \PHPUnit_Framework_TestCase
{

	public $formatter;

	public function setUp()
	{
		$this->formatter = new DefaultFormatter;
	}

	public function test_formatClassName_propertyFormatsClassName()
	{
		$className = 'TestEntity';
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->formatter, 'formatClassName' );
		$actual = $method->invoke( $this->formatter, $className );

		$this->assertEquals( 'TestEntityMock', $actual );
	}

	public function test_outputformat()
	{
		$class = new \Minion\MockMakerBundle\Tests\Resources\Entities\Badge;
		//$class = new \Minion\MockMakerBundle\Tests\Resources\Entities\Challenge;
		/* @var $mm Minion\MockMakerBundle\Library\MockMaker */
		$mm = new \Minion\MockMakerBundle\Library\MockMaker( $class, $this->formatter );
		$mm->analyzeClass();

		//DebuggerMinion::dbug( $mm, "MockMaker", TRUE );

		$code = $mm->getBasicMockCode();

		DebuggerMinion::dbug( $code, "code", TRUE );
	}

}
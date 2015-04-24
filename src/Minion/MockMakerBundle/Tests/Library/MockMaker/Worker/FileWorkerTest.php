<?php

/**
 *	FileWorkerTest
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 20, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Library\Worker;

use Minion\MockMakerBundle\Library\MockMaker\Worker\FileWorker;
use Minion\UnitTestBundle\Library\DebuggerMinion;

class FileWorkerTest extends \PHPUnit_Framework_TestCase
{

	public $worker;

	public function setUp()
	{
		$this->worker = new FileWorker;
	}

	public function qualifiedClassNameProvider()
	{
		return array(
			array( 'Minion\MockMakerBundle\Tests\Resources\Entities\MethodWorkerEntity', '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/Minion/MockMakerBundle/Tests/Resources/Entities/MethodWorkerEntity.php' ),
			array( 'Minion\MockMakerBundle\Tests\Resources\Entities\PropertyWorkerEntity', '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/Minion/MockMakerBundle/Tests/Resources/Entities/PropertyWorkerEntity.php' ),
			array( 'Minion\MockMakerBundle\Tests\Resources\Entities\SimpleEntity', '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/Minion/MockMakerBundle/Tests/Resources/Entities/SimpleEntity.php' ),
			array( 'Minion\MockMakerBundle\Tests\Resources\Entities\TestEntity', '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/Minion/MockMakerBundle/Tests/Resources/Entities/TestEntity.php' ),
			array( 'Minion\MockMakerBundle\Tests\Resources\Entities\SubEntities\SimpleSubEntity', '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/Minion/MockMakerBundle/Tests/Resources/Entities/SubEntities/SimpleSubEntity.php' ),
			array( 'Minion\MockMakerBundle\Tests\Resources\Entities\SubEntities\SimpleSubEntityUnderscore', '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/Minion/MockMakerBundle/Tests/Resources/Entities/SubEntities/SimpleSubEntityUnderscore.php' ),
		);
	}

	/**
	 * @dataProvider qualifiedClassNameProvider
	 */
	public function test_getClassInstance( $expected, $path )
	{
		$actual = $this->worker->getQualifiedClassName( $path );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @dataProvider qualifiedClassNameProvider
	 */
	public function test_getClassInstance_withValidNamespaces( $expected, $path )
	{
		$this->worker->addClassNamespacePathToArray('Minion\MockMakerBundle\Tests\Resources\Entities\SomeEntity');
		$actual = $this->worker->getQualifiedClassName( $path );
		$this->assertEquals( $expected, $actual );
	}

}
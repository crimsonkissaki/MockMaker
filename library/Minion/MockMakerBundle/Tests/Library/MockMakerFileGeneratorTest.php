<?php

/**
 *	FileDataGeneratorTest
 *
 *	@author		Evan Johnson
 *	@created	Apr 20, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Library;

use Minion\MockMakerBundle\Library\FileDataGenerator;
use Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerErrors;
use Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerException;

use Minion\UnitTestBundle\Library\DebuggerMinion;
use Minion\UnitTestBundle\Library\ReflectionMinion;

class FileDataGeneratorTest extends \PHPUnit_Framework_TestCase
{

	const PROJECT_ROOT_DIRECTORY = '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/';

	/* @var $generator FileDataGenerator */
	public $generator;

	public $pathToEntities;

	public $pathToGeneratedEntities;

	public function setUp()
	{
		$this->generator = new FileDataGenerator;
		$this->pathToEntities = dirname(dirname(__FILE__)) . '/Resources/Entities/';
		$this->pathToGeneratedEntities = dirname(dirname(__FILE__)) . '/Resources/GeneratedEntities/';
	}

	public function test_settingOptionsCorrectlySetsFileGeneratorOptions()
	{
		$this->generator->getOptions()->setOverwriteExistingFiles( TRUE );
		$this->generator->getOptions()->setRecursiveRead( FALSE );

		$this->assertTrue( $this->generator->getOptions()->getOverwriteExistingFiles() );
		$this->assertFalse( $this->generator->getOptions()->getRecursiveRead() );
	}

	public function test_addFilesToMock_addsSingleFileToList()
	{
		$file = $this->pathToEntities . 'SimpleEntity.php';
		$this->generator->addFilesToMock( $file );

		$this->assertEquals( 1, count($this->generator->getFilesToMock()) );
	}

	public function test_addFilesToMock_addsArrayOfFilesToList()
	{
		$files = array(
			$this->pathToEntities . 'SimpleEntity.php',
			$this->pathToEntities . 'MethodWorkerEntity.php',
			$this->pathToEntities . 'PropertyWorkerEntity.php',
			$this->pathToEntities . 'TestEntity.php',
		);
		$this->generator->addFilesToMock( $files );

		$this->assertEquals( 4, count($this->generator->getFilesToMock()) );
	}

	/**
	 * @expectedException Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerException
	 */
	public function test_validateReadDirectory_throwsProperErrorWithInvalidDirectory()
	{
		$this->generator->getOptions()->setReadDirectory( 'qwerty' );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'validateReadDirectory' );
		$method->invoke( $this->generator );
	}

	public function test_validateReadDirectory_returnsTrueForTestingEntitiesDirectory()
	{
		$this->generator->getOptions()->setReadDirectory( $this->pathToEntities );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'validateReadDirectory' );
		$actual = $method->invoke( $this->generator );

		$this->assertTrue( $actual );
	}

	/**
	 * @expectedException Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerException
	 */
	public function test_validateWriteDirectory_throwsProperErrorWithInvalidDirectory()
	{
		$this->generator->getOptions()->setWriteDirectory( 'qwerty' );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'validateWriteDirectory' );
		$actual = $method->invoke( $this->generator );

		$this->assertTrue( $actual );
	}

	public function test_validateWriteDirectory_returnsTrueForTestingEntitiesDirectory()
	{
		$this->generator->getOptions()->setWriteDirectory( $this->pathToEntities );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'validateWriteDirectory' );

		$this->assertTrue( $method->invoke( $this->generator ) );
	}

	public function test_addFilesFromReadDirectoryToFilesToMock_returnsFalseIfNoReadDirectorySpecified()
	{
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'addFilesFromReadDirectoryToFilesToMock' );

		$this->assertFalse( $method->invoke( $this->generator ) );
		$this->assertEquals( 0, count($this->generator->getFilesToMock() ) );
	}

	public function test_addFilesFromReadDirectoryToFilesToMock_addsFilesFromReadDirectoryIfReadDirectorySpecified()
	{
		$this->generator->getOptions()->setReadDirectory( $this->pathToEntities );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'addFilesFromReadDirectoryToFilesToMock' );

		$this->assertTrue( $method->invoke( $this->generator ) );
		$this->assertEquals( 6, count($this->generator->getFilesToMock() ) );
	}

	/**
	 * @expectedException Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerException
	 */
	public function test_validateFilesToMock_throwsExceptionWithInvalidFile()
	{
		$file = $this->pathToEntities . 'InvalidFile.php';
		$this->generator->addFilesToMock( $file );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'validateFilesToMock' );

		$actual = $method->invoke( $this->generator );
	}

	public function test_validateFilesToMock_returnsTrueForValidFile()
	{
		$file = $this->pathToEntities . 'SimpleEntity.php';
		$this->generator->addFilesToMock( $file );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'validateFilesToMock' );

		$this->assertTrue( $method->invoke( $this->generator ) );
	}

	public function _test_getFilesToMockFromReadDirectory_returnsAllFilesFromReadDirectory()
	{
		$this->generator->getOptions()->setReadDirectory( $this->pathToEntities );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'getFilesToMockFromReadDirectory' );
		$actual = $method->invoke( $this->generator );

		$this->assertEquals( 4, count($actual) );
	}

	public function test_getFilesToMockFromReadDirectory_returnsAllFilesFromReadDirectoryRecursively()
	{
		$this->generator->getOptions()->setReadDirectory( $this->pathToEntities );
		$this->generator->getOptions()->setRecursiveRead( TRUE );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'getFilesToMockFromReadDirectory' );
		$actual = $method->invoke( $this->generator );

		$this->assertEquals( 8, count($actual) );
	}

	public function test_generateMockFileNamespace_returnsCorrectNamespace()
	{
		$this->generator->getOptions()->setWriteDirectory( $this->pathToGeneratedEntities );
		$this->generator->setProjectRootDir( '/Applications/XAMPP/xamppfiles/htdocs/mockMaker/src/' );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'generateMockFileNamespace' );
		$actual = $method->invoke( $this->generator );
		$expected = 'Minion\MockMakerBundle\Tests\Resources\GeneratedEntities';

		$this->assertEquals( $expected, $actual );
	}

	public function projectRootDirProvider()
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
	 * @dataProvider projectRootDirProvider
	 */
	public function test_determineProjectRootDir_returnsProperRootDirectory( $namespace, $filePath )
	{
		$this->generator->addFilesToMock( $filePath );
		$this->generator->addMockClassNamespace( $namespace );
		$method = ReflectionMinion::getAccessibleNonPublicMethod( $this->generator, 'determineProjectRootDir' );
		$actual = $method->invoke( $this->generator );
		$expected = self::PROJECT_ROOT_DIRECTORY;

		$this->assertEquals( $expected, $actual );
	}

	public function _test_processFilesToMock_processesCorrectly()
	{
		$this->generator->getOptions()->setWriteDirectory( $this->pathToGeneratedEntities );
		$this->generator->getOptions()->setReadDirectory( $this->pathToEntities );
		$this->generator->setRecursiveRead( TRUE );
		$this->generator->generateMockFiles();
	}

}

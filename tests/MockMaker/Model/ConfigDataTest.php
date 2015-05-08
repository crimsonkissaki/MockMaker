<?php

/**
 * 	ConfigDataTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 24, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

use MockMaker\Model\ConfigData;
use MockMaker\TestHelper\TestHelper;
use MockMaker\Exception\MockMakerException;

class ConfigDataTest extends \PHPUnit_Framework_TestCase
{
    /* @var $config ConfigData */

    public $config;
    public $pathToEntities;
    public $pathToGeneratedEntities;
    public $rootDir;

    public function setUp()
    {
        $this->rootDir = dirname(dirname(__FILE__)) . '/';
        $this->config = new ConfigData();
        $this->pathToEntities = dirname(dirname(__FILE__)) . '/Entities/';
    }

    public function test_addFilesToMock_addsSingleFileToList()
    {
        $file = $this->pathToEntities . 'SimpleEntity.php';
        $this->config->addFilesToMock($file);
        $this->assertEquals(1, count($this->config->getFilesToMock()));
    }

    public function test_addFilesToMock_addsArrayOfFilesToList()
    {
        $files = array(
            $this->pathToEntities . 'SimpleEntity.php',
            $this->pathToEntities . 'MethodWorkerEntity.php',
            $this->pathToEntities . 'TestEntity.php',
        );
        $this->config->addFilesToMock($files);
        $this->assertEquals(3, count($this->config->getFilesToMock()));
    }

    public function test_addFilesToAllDetectedFiles_addsSingleFileToList()
    {
        $file = $this->pathToEntities . 'SimpleEntity.php';
        $this->config->addToAllDetectedFiles($file);
        $this->assertEquals(1, count($this->config->getAllDetectedFiles()));
    }

    public function test_addFilesToAllDetectedFiles_addsArrayOfFilesToList()
    {
        $files = array(
            $this->pathToEntities . 'SimpleEntity.php',
            $this->pathToEntities . 'MethodWorkerEntity.php',
            $this->pathToEntities . 'PropertyWorkerEntity.php',
            $this->pathToEntities . 'TestEntity.php',
        );
        $this->config->addToAllDetectedFiles($files);
        $this->assertEquals(4, count($this->config->getAllDetectedFiles()));
    }

    public function test_addReadDirectories_addsSingleDirectory()
    {
        $this->config->addReadDirectories(dirname(__FILE__));
        $this->assertEquals(1, count($this->config->getReadDirectories()));
    }

    public function test_addReadDirectories_addsSingleDirectory_addsTrailingSlashIfNotPresent()
    {
        $this->config->addReadDirectories(dirname(__FILE__));
        $expected = dirname(__FILE__) . "/";
        $dirs = $this->config->getReadDirectories();
        $actual = $dirs[0];
        $this->assertEquals($expected, $actual);
    }

    public function test_addReadDirectories_addsArrayOfDirectories()
    {
        $dirs = array(
            dirname(__FILE__),
            dirname(dirname(__FILE__)),
            dirname(dirname(dirname(__FILE__))),
        );
        $this->config->addReadDirectories($dirs);
        $this->assertEquals(3, count($this->config->getReadDirectories()));
    }

    public function test_addReadDirectories_addsArrayOfDirectories_addsTrailingSlashToEachEntryIfNotPresent()
    {
        $dirs = array(
            dirname(__FILE__),
            dirname(dirname(__FILE__)),
            dirname(dirname(dirname(__FILE__))),
        );
        $this->config->addReadDirectories($dirs);
        $expected = array(
            dirname(__FILE__) . "/",
            dirname(dirname(__FILE__)) . "/",
            dirname(dirname(dirname(__FILE__))) . "/",
        );
        $this->assertEquals($expected, $this->config->getReadDirectories());
    }

    /**
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_addReadDirectories_throwsExceptionIfBadDirectoryIsInArrayArg()
    {
        $dirs = array(
            $this->rootDir,
            $this->rootDir . 'totallyfakedir',
        );
        $this->config->addReadDirectories($dirs);
    }

    /**
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_addReadDirectories_throwsExceptionIfArgumentIsBadDirectory()
    {
        $dir = $this->rootDir . 'totallyfakedir';
        $this->config->addReadDirectories($dir);
    }


    // Cannot test setMockWriteDir for thrown exceptions on bad directories, since it makes them
    public function test_setMockWriteDirectory_trimsCorrectly()
    {
        $this->config->setMockWriteDir(" {$this->rootDir} ");
        $this->assertEquals($this->rootDir, $this->config->getMockWriteDir());
    }

    public function test_setMockWriteDirectory_addsTrailingSlashIfNonePresent()
    {
        $this->config->setMockWriteDir($this->rootDir);
        $this->assertEquals($this->rootDir, $this->config->getMockWriteDir());
    }

    public function test_setProjectRootPath_TrimsCorrectly()
    {
        $this->config->setProjectRootPath(" {$this->rootDir} ");
        $this->assertEquals($this->rootDir, $this->config->getProjectRootPath());
    }

    public function test_setProjectRootPath_addsTrailingSlashIfNotPresent()
    {
        $this->config->setProjectRootPath($this->rootDir);
        $expected = $this->rootDir;
        $this->assertEquals($expected, $this->config->getProjectRootPath());
    }

    /**
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_setProjectRootPath_throwsExceptionForInvalidRootPath()
    {
        $this->config->setProjectRootPath($this->rootDir.'notarealfolder');
    }


    /**
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_setReadDirectories_throwsExceptionForInvalidDir()
    {
        $badDir = array( dirname(__FILE__) . "notarealdir/" );
        $this->config->setReadDirectories( $badDir );
    }

    public function test_setMockWriteDirectory_createsNonExistentDirectory()
    {
        $badDir = dirname(dirname(dirname(__FILE__))) . '/CreatedWriteDirectory';
        $this->assertFalse(is_dir($badDir));
        $this->config->setMockWriteDir($badDir);
        $this->assertTrue(is_dir($badDir));
        rmdir($badDir);
    }

    public function test_setMockUnitTestWriteDirectory_createsNonExistentDirectory()
    {
        $badDir = dirname(dirname(dirname(__FILE__))) . '/CreatedUnitTestWriteDirectory';
        $this->assertFalse(is_dir($badDir));
        $this->config->setMockWriteDir($badDir);
        $this->assertTrue(is_dir($badDir));
        rmdir($badDir);
    }

}

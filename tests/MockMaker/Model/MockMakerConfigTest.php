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
use MockMaker\Helper\TestHelper;
use MockMaker\Exception\MockMakerException;

class ConfigDataTest extends \PHPUnit_Framework_TestCase
{
    /* @var $config ConfigData */

    public $config;
    public $pathToEntities;
    public $pathToGeneratedEntities;

    public function setUp()
    {
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
        $this->config->addFilesToAllDetectedFiles($file);
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
        $this->config->addFilesToAllDetectedFiles($files);
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

    // Cannot test setMockWriteDirectory for thrown exceptions on bad directories, since it makes them
    public function test_setMockWriteDirectory_trimsCorrectly()
    {
        $root = dirname(dirname(__FILE__));
        $this->config->setMockWriteDirectory(" {$root} ");
        $this->assertEquals($root . "/", $this->config->getMockWriteDirectory());
    }

    public function test_setMockWriteDirectory_addsTrailingSlashIfNonePresent()
    {
        $root = dirname(dirname(__FILE__));
        $this->config->setMockWriteDirectory($root);
        $this->assertEquals($root . "/", $this->config->getMockWriteDirectory());
    }

    public function test_setProjectRootPath_TrimsCorrectly()
    {
        $root = dirname(dirname(__FILE__));
        $this->config->setProjectRootPath(" {$root} ");
        $this->assertEquals($root . "/", $this->config->getProjectRootPath());
    }

    public function test_setProjectRootPath_addsTrailingSlashIfNotPresent()
    {
        $root = dirname(dirname(__FILE__));
        $this->config->setProjectRootPath($root);
        $expected = $root . "/";
        $this->assertEquals($expected, $this->config->getProjectRootPath());
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
        $this->config->setMockWriteDirectory($badDir);
        $this->assertTrue(is_dir($badDir));
        rmdir($badDir);
    }

    public function test_setMockUnitTestWriteDirectory_createsNonExistentDirectory()
    {
        $badDir = dirname(dirname(dirname(__FILE__))) . '/CreatedUnitTestWriteDirectory';
        $this->assertFalse(is_dir($badDir));
        $this->config->setMockWriteDirectory($badDir);
        $this->assertTrue(is_dir($badDir));
        rmdir($badDir);
    }

}

<?php

/**
 * 	MockMakerTest
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 26, 2015
 * 	@version	1.0
 */

namespace MockMaker;

use MockMaker\MockMaker;

class MockMakerTest extends \PHPUnit_Framework_TestCase
{
    /* @var $mockMaker MockMaker */

    public $mockMaker;
    public $rootDir;
    public $entitiesDir;

    public function setUp()
    {
        $this->mockMaker = new MockMaker();
        $this->rootDir = dirname(dirname(__FILE__));
        $this->entitiesDir = $this->rootDir . '/tests/MockMaker/Entities/';
    }

    /**
     * These tests verify that MockMaker interfaces with the
     * MockMakerConfig class properly.
     */
    public function test_setRootDirectory()
    {
        $this->mockMaker->setRootDirectory($this->rootDir);
        $this->assertEquals($this->rootDir, $this->mockMaker->getConfig()->getRootDirectory());
    }

    public function test_mockFiles_addsFiles()
    {
        $this->mockMaker->mockFiles($this->entitiesDir . 'SimpleEntity.php');
        $this->assertEquals(1, count($this->mockMaker->getConfig()->getFilesToMock()));
    }

    public function test_getFilesFrom()
    {
        $this->mockMaker->getFilesFrom($this->entitiesDir);
        $actual = $this->mockMaker->getConfig()->getReadDirectories();
        $this->assertEquals(1, count($actual));
        $this->assertEquals($this->entitiesDir, $actual[0]);
    }

    public function test_recursively()
    {
        $this->assertFalse($this->mockMaker->getConfig()->getRecursiveRead());
        $this->mockMaker->recursively();
        $this->assertTrue($this->mockMaker->getConfig()->getRecursiveRead());
    }

    public function test_saveFilesTo()
    {
        $this->mockMaker->saveFilesTo($this->entitiesDir);
        $this->assertEquals($this->entitiesDir, $this->mockMaker->getConfig()->getWriteDirectory());
    }

    public function test_ignoreDirectoryStructure()
    {
        $this->assertTrue($this->mockMaker->getConfig()->getPreserveDirectoryStructure());
        $this->mockMaker->ignoreDirectoryStructure();
        $this->assertFalse($this->mockMaker->getConfig()->getPreserveDirectoryStructure());
    }

    public function test_overwriteExistingFiles()
    {
        $this->assertFalse($this->mockMaker->getConfig()->getOverwriteExistingFiles());
        $this->mockMaker->overwriteExistingFiles();
        $this->assertTrue($this->mockMaker->getConfig()->getOverwriteExistingFiles());
    }

    public function test_excludeFilesWithFormat()
    {
        $expected = '/Repository$/';
        $this->mockMaker->excludeFilesWithFormat($expected);
        $this->assertEquals($expected, $this->mockMaker->getConfig()->getExcludeFileRegex());
    }

    public function test_includeFilesWithFormat()
    {
        $expected = '/Entity$/';
        $this->mockMaker->includeFilesWithFormat($expected);
        $this->assertEquals($expected, $this->mockMaker->getConfig()->getIncludeFileRegex());
    }

}

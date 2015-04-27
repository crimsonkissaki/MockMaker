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
use MockMaker\Helper\TestHelper;

class MockMakerTest extends \PHPUnit_Framework_TestCase
{
    /* @var $mockMaker MockMaker */

    public $mockMaker;
    public $rootDir;
    public $entitiesDir;

    public function setUp()
    {
        $this->mockMaker = new MockMaker();
        $this->rootDir = dirname(dirname(dirname(__FILE__)));
        $this->entitiesDir = $this->rootDir . '/tests/MockMaker/Entities/';
    }

    /**
     * Used for testing workflow.
     */
    public function _test_workflow()
    {
        $actual = $this->mockMaker
            ->getFilesFrom($this->entitiesDir)
            ->recursively()
            ->excludeFilesWithFormat('/^Method/')
            ->verifySettings();

        TestHelper::dbug($actual, __METHOD__, true);
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
        $this->assertEquals(1, count($this->mockMaker->getConfig()->getAllDetectedFiles()));
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

    public function test_testRegexPatterns_returnsCorrectFilesWithOnlyExcludeRegex()
    {
        $regex = '/Underscore$/';
        $actual = $this->mockMaker
            ->getFilesFrom($this->entitiesDir . 'SubEntities/')
            ->excludeFilesWithFormat($regex)
            ->testRegexPatterns();
        $expected = array(
            'include' => array(
                $this->entitiesDir . 'SubEntities/SimpleSubEntity.php',
                $this->entitiesDir . 'SubEntities/SimpleSubEntityUnderscore.php'
            ),
            'exclude' => array(
                $this->entitiesDir . 'SubEntities/SimpleSubEntityUnderscore.php'
            ),
            'workable' => array(
                $this->entitiesDir . 'SubEntities/SimpleSubEntity.php',
            ),
        );

        $this->assertEquals($expected, $actual);
    }

    public function test_testRegexPatterns_returnsCorrectFilesWithOnlyIncludeRegex()
    {
        $regex = '/Entity$/';
        $actual = $this->mockMaker
            ->getFilesFrom($this->entitiesDir . 'SubEntities/')
            ->includeFilesWithFormat($regex)
            ->testRegexPatterns();
        $expected = array(
            'include' => array(
                $this->entitiesDir . 'SubEntities/SimpleSubEntity.php',
            ),
            'exclude' => array(),
            'workable' => array(
                $this->entitiesDir . 'SubEntities/SimpleSubEntity.php',
            ),
        );

        $this->assertEquals($expected, $actual);
    }

    public function test_testRegexPatterns_returnsCorrectFilesWithBothIncludeAndExcludeRegex()
    {
        $i_regex = '/Entity$/';
        $e_regex = '/^Simple.*$/';
        $actual = $this->mockMaker
            ->getFilesFrom($this->entitiesDir)
            ->recursively()
            ->includeFilesWithFormat($i_regex)
            ->excludeFilesWithFormat($e_regex)
            ->testRegexPatterns();
        $expected = array(
            'include' => array(
                $this->entitiesDir . 'MethodWorkerEntity.php',
                $this->entitiesDir . 'PropertyWorkerEntity.php',
                $this->entitiesDir . 'SimpleEntity.php',
                $this->entitiesDir . 'TestEntity.php',
                $this->entitiesDir . 'SubEntities/SimpleSubEntity.php',
            ),
            'exclude' => array(
                $this->entitiesDir . 'SimpleEntity.php',
                $this->entitiesDir . 'SubEntities/SimpleSubEntityUnderscore.php',
            ),
            'workable' => array(
                $this->entitiesDir . 'MethodWorkerEntity.php',
                $this->entitiesDir . 'PropertyWorkerEntity.php',
                $this->entitiesDir . 'TestEntity.php',
                $this->entitiesDir . 'SubEntities/SimpleSubEntity.php',
            ),
        );

        $this->assertEquals(sort($expected), sort($actual));
    }

}

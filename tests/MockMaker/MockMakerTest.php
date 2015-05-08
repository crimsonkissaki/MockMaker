<?php

/**
 * MockMakerTest
 *
 * This only tests to ensure the options set the proper class properties.
 *
 * @author        Evan Johnson
 * @created       Apr 26, 2015
 * @version       1.0
 */

namespace MockMaker;

use MockMaker\MockMaker;
use MockMaker\Exception\MockMakerErrors;
use MockMaker\Exception\MockMakerException;
use MockMaker\Worker\CodeWorker;
use MockMaker\TestHelper\TestHelper;

class MockMakerTest extends \PHPUnit_Framework_TestCase
{

    /* @var $mockMaker MockMaker */
    public $mockMaker;
    public $rootDir;
    public $entitiesDir;
    public $unitTestsWriteDir;
    public $testResourcesDir;

    public function setUp()
    {
        $this->mockMaker = new MockMaker();
        $this->rootDir = dirname(dirname(dirname(__FILE__)));
        $this->testResourcesDir = $this->rootDir . '/tests/MockMaker/Resources/';
        $this->entitiesDir = $this->rootDir . '/tests/MockMaker/Entities/';
        $this->unitTestsWriteDir = $this->rootDir . '/tests/MockMaker/EntitiesUnitTests/';
    }

    /**
     * Used for testing workflow.
     */
    public function _test_workflow()
    {
        $actual = $this->mockMaker
            ->mockTheseEntities($this->entitiesDir . 'TestEntity.php')
            //->mockEntitiesInDirectory($this->entitiesDir)
            //->recursively()
            ->saveMockFilesIn($this->rootDir . '/tests/MockMaker/Mocks/Entities')
            //->excludeFilesWithFormat('/^Method/')
            //->saveMocksWithFileNameFormat('Mock_%FileName%')
            //->overwriteMockFiles()
            //->useBaseNamespaceForMocks('MockMaker\Mocks\Entities')
            ->saveUnitTestsIn($this->unitTestsWriteDir)
            ->createMocks();
        //->verifySettings();

        TestHelper::dbug($actual, __METHOD__, true);
    }

    public function test_endToEndMockCreation()
    {
        $expected = file_get_contents($this->testResourcesDir . 'TestEntityCode.txt');
        $actual = $this->mockMaker
            ->mockTheseEntities($this->entitiesDir . 'TestEntity.php')
            //->saveMockFilesIn($this->rootDir . '/tests/MockMaker/Mocks/Entities')
            ->createMocks();
        $this->assertEquals($expected, $actual);
    }

    /**
     * These tests verify that MockMaker interfaces with the
     * ConfigData class properly.
     */

    public function test_setProjectRootPath()
    {
        $this->mockMaker->setProjectRootPath($this->rootDir);
        $this->assertEquals($this->rootDir . '/', $this->mockMaker->getConfig()->getProjectRootPath());
    }

    public function test_mockTheseFiles_addsSingleFile()
    {
        $this->mockMaker->mockTheseEntities($this->entitiesDir . 'SimpleEntity.php');
        $this->assertEquals(1, count($this->mockMaker->getConfig()->getAllDetectedFiles()));
    }

    public function test_mockTheseFiles_addsArrayOfFiles()
    {
        $files = array(
            $this->entitiesDir . 'TestEntity.php',
            $this->entitiesDir . 'SimpleEntity.php',
        );
        $this->mockMaker->mockTheseEntities($files);
        $this->assertEquals(2, count($this->mockMaker->getConfig()->getAllDetectedFiles()));
    }

    public function test_mockFilesIn()
    {
        $this->mockMaker->mockEntitiesInDirectory($this->entitiesDir);
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

    public function test_saveMockFilesIn()
    {
        $this->mockMaker->saveMockFilesIn($this->entitiesDir);
        $this->assertEquals($this->entitiesDir, $this->mockMaker->getConfig()->getMockWriteDir());
    }

    public function test_ignoreDirectoryStructure()
    {
        $this->assertTrue($this->mockMaker->getConfig()->getPreserveDirStructure());
        $this->mockMaker->ignoreDirectoryStructure();
        $this->assertFalse($this->mockMaker->getConfig()->getPreserveDirStructure());
    }

    public function test_overwriteExistingFiles()
    {
        $this->assertFalse($this->mockMaker->getConfig()->getOverwriteMockFiles());
        $this->mockMaker->overwriteExistingMockFiles();
        $this->assertTrue($this->mockMaker->getConfig()->getOverwriteMockFiles());
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

    public function test_useThisMockTemplate()
    {
        $expected = 'CustomTemplate.php';
        $this->mockMaker->useThisMockTemplate($expected);
        $this->assertEquals($expected, $this->mockMaker->getConfig()->getMockDataPointWorker()->getMockTemplate());
    }

    public function test_useThisCodeWorker()
    {
        $expected = new CodeWorker();
        $customTemplate = 'CustomTemplate.php';
        $expected->setMockTemplate($customTemplate);
        $this->mockMaker->useCustomDataPointWorker($expected);
        $this->assertEquals($expected, $this->mockMaker->getConfig()->getMockDataPointWorker());
    }

    public function test_saveMocksWithFileNameFormat()
    {
        $expected = 'Mock%FileName%';
        $this->mockMaker->saveMocksWithFileNameFormat($expected);
        $this->assertEquals($expected, $this->mockMaker->getConfig()->getMockFileNameFormat());
    }

    public function test_useBaseNamespaceForMocks()
    {
        $expected = 'MockMaker\Mocks\Entities';
        $this->mockMaker->useBaseNamespaceForMocks($expected);
        $this->assertEquals($expected, $this->mockMaker->getConfig()->getMockFileBaseNamespace());
    }

    public function  test_saveUnitTestsTo()
    {
        $this->mockMaker->saveUnitTestsIn($this->unitTestsWriteDir);
        $this->assertEquals($this->unitTestsWriteDir, $this->mockMaker->getConfig()->getUnitTestWriteDir());
    }

    public function test_verifySettings()
    {
        $actual = $this->mockMaker->verifySettings();
        $this->assertInstanceOf('MockMaker\Model\ConfigData', $actual);
    }

    public function test_testRegexPatterns()
    {
        $regex = '/Underscore$/';
        $actual = $this->mockMaker
            ->mockEntitiesInDirectory($this->entitiesDir . 'SubEntities/')
            ->excludeFilesWithFormat($regex)
            ->testRegexPatterns();
        $this->assertArrayHasKey('include', $actual);
        $this->assertArrayHasKey('exclude', $actual);
        $this->assertArrayHasKey('workable', $actual);
    }

}

<?php
/**
 * MockDataWorkerTest
 *
 * @package:
 * @author :     johnsone
 * @created:    5/7/15
 */

namespace MockMaker\Worker;

use MockMaker\TestHelper\TestHelper;
use MockMaker\Model\ConfigData;

class MockDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    /* @var $worker MockDataWorker */
    public $worker;
    /* @var $config ConfigData */
    public $config;
    public $rootDir;

    public function setUp()
    {
        $this->worker = new MockDataWorker();
        $this->rootDir = TestHelper::getRootDir();
        $this->config = new ConfigData();
        $this->config->setProjectRootPath($this->rootDir);
    }

    public function test_formatMockClassName_returnsProperMockName()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'formatMockClassName');
        $actual = $method->invoke($this->worker, 'SimpleEntity', $this->config->getMockFileNameFormat());
        $this->assertEquals('SimpleEntityMock', $actual);
    }

    public function test_determineMockFileSavePath_returnsTargetFilePathPlusMockFileName_ifNoMockWriteDir()
    {
        $targetFilePath = $this->rootDir . 'tests/MockMaker/Entities/';
        $expected = $targetFilePath;
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineMockFileSavePath');
        $actual = $method->invoke($this->worker, $targetFilePath, $this->config);
        $this->assertEquals($expected, $actual);
    }

    public function test_determineMockFileSavePath_returnsMockWriteDirPlusMockFileName_ifNoReadDirs()
    {
        $mockWriteDir = $this->rootDir . 'tests/MockMaker/Mocks/Entities/';
        $targetFilePath = $this->rootDir . 'tests/MockMaker/Entities/';
        $expected = $mockWriteDir;
        $this->config->setMockWriteDir($mockWriteDir);
        $this->config->setPreserveDirStructure(true);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineMockFileSavePath');
        $actual = $method->invoke($this->worker, $targetFilePath, $this->config);
        $this->assertEquals($expected, $actual);
    }

    public function test_determineMockFileSavePath_returnsMockWriteDirPlusMockFileName_ifNoPreserveDirStructure()
    {
        $mockWriteDir = $this->rootDir . 'tests/MockMaker/Mocks/Entities/';
        $targetFilePath = $this->rootDir . 'tests/MockMaker/Entities/';
        $expected = $mockWriteDir;
        $this->config->setMockWriteDir($mockWriteDir);
        $this->config->addReadDirectories($targetFilePath);
        $this->config->setPreserveDirStructure(false);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineMockFileSavePath');
        $actual = $method->invoke($this->worker, $targetFilePath, $this->config);
        $this->assertEquals($expected, $actual);
    }

    public function findTargetFileOriginDirProvider()
    {
        $rootDir = TestHelper::getRootDir();

        return array(
            array($rootDir . 'tests/SubEntities/', false),
            array($rootDir . 'tests/Entities/', $rootDir . 'tests/Entities/'),
            array($rootDir . 'tests/Entities/SubEntities/', $rootDir . 'tests/Entities/'),
            array($rootDir . 'tests/Entities/SubEntities/SubSubEntities/', $rootDir . 'tests/Entities/'),
        );
    }

    /**
     * @dataProvider findTargetFileOriginDirProvider
     */
    public function test_findTargetFileOriginDir_returnsCorrectResponse($targetFileDir, $expected)
    {
        $readDirs = array(
            $this->rootDir . 'tests/Entities/',
            $this->rootDir . 'tests/Entities/SubEntities/',
        );
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'findTargetFileOriginDir');
        $actual = $method->invoke($this->worker, $readDirs, $targetFileDir);
        $this->assertEquals($expected, $actual);
    }

    public function generateMockFileSavePathProvider()
    {
        $rootDir = TestHelper::getRootDir();

        return array(
            array(
                $rootDir . 'tests/MockMaker/Entities/',
                $rootDir . 'tests/MockMaker/Mocks/Entities/'
            ),
            array(
                $rootDir . 'tests/MockMaker/Entities/SubEntities/',
                $rootDir . 'tests/MockMaker/Mocks/Entities/SubEntities/'
            ),
            array(
                $rootDir . 'tests/MockMaker/Entities/SubEntities/SubSubEntities/',
                $rootDir . 'tests/MockMaker/Mocks/Entities/SubEntities/SubSubEntities/'
            ),
        );
    }

    /**
     * @dataProvider generateMockFileSavePathProvider
     */
    public function test_determineMockFileSavePath_returnsCorrectSavePathForSubDirFile($targetFilePath, $expected)
    {
        $root = TestHelper::getRootDir();
        $this->config->setMockWriteDir($root . 'tests/MockMaker/Mocks/Entities');
        $readDirs = array(
            $root . 'tests/MockMaker/Entities',
            $root . 'tests/MockMaker/Entities/SubEntities',
        );
        $this->config->addReadDirectories($readDirs);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineMockFileSavePath');
        $actual = $method->invoke($this->worker, $targetFilePath, $this->config);
        $this->assertEquals($expected, $actual);
    }

    public function determineWithBaseNamespaceProvider()
    {
        $root = TestHelper::getRootDir();

        return array(
            array(
                $root . 'tests/MockMaker/Mocks/Entities/',
                'MockMaker\Mocks\Entities',
                'MockMaker\Mocks\Entities'
            ),
            array(
                $root . 'tests/MockMaker/Mocks/Entities/SubDirectory/',
                'MockMaker\Mocks\Entities',
                'MockMaker\Mocks\Entities\SubDirectory'
            ),
            array(
                $root . 'tests/MockMaker/Mocks/Entities/SubDirectory/SubSubDirectory/',
                'MockMaker\Mocks\Entities',
                'MockMaker\Mocks\Entities\SubDirectory\SubSubDirectory'
            ),
            array(
                $root . 'tests/MockMaker/Mocks/Entities/SubDirectory/SubSubDirectory/HolyCrapSeriously/',
                'MockMaker\Mocks\Entities',
                'MockMaker\Mocks\Entities\SubDirectory\SubSubDirectory\HolyCrapSeriously'
            ),
            array(
                $root . 'tests/MockMaker/Mocks/Entities/SubDirectory/SubSubDirectory/HolyCrapSeriously/SubDirectory/',
                'MockMaker\Mocks\Entities\SubDirectory',
                'MockMaker\Mocks\Entities\SubDirectory\SubSubDirectory\HolyCrapSeriously\SubDirectory'
            ),
        );
    }

    /**
     * @dataProvider determineWithBaseNamespaceProvider
     */
    public function test_determineWithBaseNamespace_returnsProperNamespace($mockPath, $baseNamespace, $expected)
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineWithBaseNamespace');
        $actual = $method->invoke($this->worker, $mockPath, $baseNamespace);
        $this->assertEquals($expected, $actual);
    }
}

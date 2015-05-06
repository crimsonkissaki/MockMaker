<?php

/**
 * 	MockMakerFileDataWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\MockMakerFileDataWorker;
use MockMaker\Model\ConfigData;
use MockMaker\TestHelper\TestHelper;

class MockMakerFileDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker MockMakerFileDataWorker
    public $worker;
    // @var $config ConfigData
    public $config;
    public $file = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->worker = new MockMakerFileDataWorker();
        $this->config = new ConfigData();
    }

    public function test_generateNewObject_returnsCorrectFullFilePath()
    {
        $actual = $this->worker->generateNewObject($this->file, $this->config);
        $this->assertEquals($this->file, $actual->getSourceFileFullPath());
    }

    public function test_generateNewObject_returnsCorrectFileName()
    {
        $actual = $this->worker->generateNewObject($this->file, $this->config);
        $this->assertEquals('SimpleEntity.php', $actual->getSourceFileName());
    }

    public function test_generateMockFileName_returnsCorrectFileName()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'generateMockFileName');
        $actual = $method->invoke($this->worker, 'TestEntity.php', '%FileName%Mock');
        $expected = 'TestEntityMock.php';
        $this->assertEquals($expected, $actual);
    }

    public function generatedMockFileNamespaceProvider()
    {
        $root = TestHelper::getRootDir();
        return array(
            array( '',
                $root . 'tests/MockMaker/Entities/SimpleEntityMock.php',
                'MockMaker\Entities'),
            array( 'MockMaker\Tests\Entities',
                $root . 'tests/MockMaker/Entities/SimpleEntityMock.php',
                'MockMaker\Tests\Entities'),
            array( 'MockMaker\Tests\Entities',
                $root . 'tests/MockMaker/Entities/Simples/SimpleEntityMock.php',
                'MockMaker\Tests\Entities\Simples'),
            array( 'MockMaker\Entities',
                $root . 'tests/MockMaker/Entities/SimpleEntityMock.php',
                'MockMaker\Entities'),
        );
    }

    /**
     * @dataProvider generatedMockFileNamespaceProvider
     */
    public function test_generateMockFileNamespace_returnsProperNamespace($baseNamespace, $testFileName, $expected)
    {
        $this->config->setMockFileBaseNamespace($baseNamespace);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'generateMockFileNamespace');
        $actual = $method->invoke($this->worker, $testFileName, $this->config);
        $this->assertEquals($expected, $actual);
    }

    public function generateMockFileNamespaceProvider()
    {
        $root = TestHelper::getRootDir();
        return array(
            array( $root.'tests/MockMaker/Entities/TestEntity.php',
                $root.'tests/MockMaker/Mocks/Entities/TestEntityMock.php' ),
            array( $root.'tests/MockMaker/Entities/SimpleEntity.php',
                $root.'tests/MockMaker/Mocks/Entities/SimpleEntityMock.php' ),
            array( $root.'tests/MockMaker/Entities/SubEntities/SimpleSubEntity.php',
                $root.'tests/MockMaker/Mocks/Entities/SubEntities/SimpleSubEntityMock.php' ),
            array( $root.'tests/MockMaker/Entities/SubEntities/SimpleSubEntityUnderscore.php',
                $root.'tests/MockMaker/Mocks/Entities/SubEntities/SimpleSubEntityUnderscoreMock.php' ),
        );
    }

    /**
     * @dataProvider generateMockFileNamespaceProvider
     */
    public function test_determineMockFileSavePath_returnsCorrectSavePathForSubDirectoryFile($fileToMock, $expected)
    {
        $root = TestHelper::getRootDir();
        $this->config->setProjectRootPath( $root );
        $this->config->setMockWriteDirectory( $root . 'tests/MockMaker/Mocks/Entities');
        $this->config->addReadDirectories( $root . 'tests/MockMaker/Entities');
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineMockFileSavePath');
        $actual = $method->invoke($this->worker, $fileToMock, $this->config);
        $this->assertEquals($expected, $actual);
    }

}

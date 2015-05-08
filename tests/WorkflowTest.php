<?php
/**
 * WorkflowTest
 *
 * @package:    
 * @author:     johnsone
 * @created:    5/6/15
 */

namespace MockMaker;

use MockMaker\MockMaker;

class WorkflowTest extends \PHPUnit_Framework_TestCase
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
        $this->rootDir = dirname(dirname(__FILE__)) . '/';
        $this->testResourcesDir = $this->rootDir . 'tests/MockMaker/Resources/';
        $this->entitiesDir = $this->rootDir . 'tests/MockMaker/Entities/';
        $this->unitTestsWriteDir = $this->rootDir . 'tests/MockMaker/EntitiesUnitTests/';
    }

    /**
     * Testing results of absolute bare minimum requirements to generate mock code
     */
    public function _test_endToEndMockCreation_bareBones()
    {
        echo "\n\n" . __METHOD__ . "\n\n";
        $expected = file_get_contents($this->testResourcesDir . 'TestEntityCode.txt');
        $actual = $this->mockMaker
            //->mockTheseEntities($this->entitiesDir . 'TestEntity.php')
            //->mockTheseEntities($this->entitiesDir . 'SimpleEntity.php')
            //->mockTheseEntities($this->entitiesDir . 'SubEntities/SimpleSubEntity.php')
            ->mockEntitiesInDirectory($this->entitiesDir)
            ->recursively()
            ->saveMockFilesIn($this->rootDir . 'tests/MockMaker/Mocks/Entities/')
            ->saveUnitTestsIn($this->rootDir . 'tests/MockMaker/Mocks/EntitiesUnitTests/')
            ->overwriteMockFiles()
            ->overwriteUnitTestFiles()
            //->useBaseNamespaceForMocks('MockMaker\Mocks\Entities')
            ->createMocks();
        $this->assertEquals($expected, $actual);
    }

    public function _test_endToEndMockCreation()
    {
        $expected = file_get_contents($this->testResourcesDir . 'TestEntityCode.txt');
        $actual = $this->mockMaker
            //->setProjectRootPath($this->rootDir)
            ->mockTheseEntities($this->entitiesDir . 'TestEntity.php')
            //->overwriteMockFiles()
            //->saveMockFilesIn($this->rootDir.'/tests/MockMaker/Mocks/Entities')
            ->createMocks();
        $this->assertEquals($expected, $actual);
    }

}

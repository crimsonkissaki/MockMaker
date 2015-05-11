<?php
/**
 * ConfigDataWorkerTest
 *
 * @package:    MockMaker
 * @author:     Evan Johnson
 * @created:    5/6/15
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;
use MockMaker\TestHelper\TestHelper;

class ConfigDataWorkerTest extends \PHPUnit_Framework_TestCase
{

    /* @var $worker ConfigDataWorker */
    public $worker;
    /* @var $config ConfigData */
    public $config;
    public $rootDir;
    public $entitiesDir;
    public $entity;

    public function setUp()
    {
        $this->rootDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
        $this->entitiesDir = $this->rootDir . 'tests/MockMaker/Entities/';
        $this->entity = $this->entitiesDir . 'TestEntity.php';

        $this->worker = new ConfigDataWorker();
        $this->config = new ConfigData();
    }

    public function test_validateRootPath_returnsUserSpecifiedDirectory()
    {
        $this->config->addToAllDetectedFiles($this->entity);
        $this->config->setProjectRootPath($this->entitiesDir);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'validateRootPath');
        $actual = $method->invoke($this->worker, $this->config);
        $this->assertEquals($this->entitiesDir, $actual);
    }

    public function test_validateRootPath_returnsCorrectRootPathIfNoUserSpecified()
    {
        $this->config->addToAllDetectedFiles($this->entity);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'validateRootPath');
        $actual = $method->invoke($this->worker, $this->config);
        $this->assertEquals($this->rootDir, $actual);
    }

    public function test_findAllTargetFiles_returnsEmptyArrayIfNoReadDirectorySpecified()
    {
        $this->config->addToAllDetectedFiles($this->entity);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'findAllTargetFiles');
        $actual = $method->invoke($this->worker, $this->config);
        $this->assertEquals([], $actual);
    }

    public function test_findAllTargetFiles_returnsFilesInDirectory_ifReadDirSpecified()
    {
        $this->config->addReadDirectories($this->entitiesDir);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'findAllTargetFiles');
        $actual = $method->invoke($this->worker, $this->config);
        $this->assertEquals(7, count($actual));
    }

    public function test_filterUnwantedFiles_returnsAllFilesIfNoFilterSpecified()
    {
        $this->config->addReadDirectories($this->entitiesDir);
        $addMethod = TestHelper::getAccessibleNonPublicMethod($this->worker, 'findAllTargetFiles');
        $files = $addMethod->invoke($this->worker, $this->config);
        $this->config->addToAllDetectedFiles($files);
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'filterUnwantedFiles');
        $actual = $method->invoke($this->worker, $this->config);
        $this->assertEquals(7, count($actual));
    }


    public function test_validateRequiredConfigData_returnsAllRequiredOptions()
    {
        $this->config->addToAllDetectedFiles($this->entitiesDir . 'TestEntity.php');
        $actual = $this->worker->validateRequiredConfigData($this->config);
        $expectedRootPath = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/';
        $this->assertEquals($expectedRootPath, $actual->getProjectRootPath());
        $this->assertEquals(1, count($actual->getAllDetectedFiles()));
        $this->assertEquals(1, count($actual->getFilesToMock()));
    }

}

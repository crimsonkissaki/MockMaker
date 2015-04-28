<?php

/**
 * 	MockMakerClassWorkerTest
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\MockMakerClassWorker;
use MockMaker\Worker\MockMakerFileWorker;
use MockMaker\Model\MockMakerFile;
use MockMaker\Model\MockMakerConfig;
use MockMaker\Helper\TestHelper;

class MockMakerClassWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker MockMakerClassWorker
    public $worker;
    // @var $fileObj MockMakerFile
    public $fileObj;
    // @var $config MockMakerConfig
    public $config;
    public $fileName = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->config = new MockMakerConfig();
        $this->config->setProjectRootPath('/Applications/XAMPP/xamppfiles/htdocs/mockmaker/');
        $fileWorker = new MockMakerFileWorker();
        $this->fileObj = $fileWorker->generateNewObject($this->fileName, $this->config);
        $this->worker = new MockMakerClassWorker();
    }

    public function test_addValidNamespaces_addsNewNamespaceToArray()
    {
        $namespace = 'MockMaker\Worker';
        $expected = array(
            $namespace,
        );
        $this->worker->addValidNamespaces($namespace);
        $this->assertEquals($expected, $this->worker->getValidNamespaces());
    }

    public function test_addValidNamespaces_doesNotAddExistingNamespaceToArray()
    {
        $namespace = 'MockMaker\Worker';
        $expected = array(
            $namespace,
        );
        $this->worker->addValidNamespaces($namespace);
        $this->worker->addValidNamespaces($namespace);
        $this->assertEquals($expected, $this->worker->getValidNamespaces());
    }

    public function test_determineClassName_returnsCorrectClassName()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'determineClassName');
        $actual = $method->invoke($this->worker, $this->fileObj);
        $this->assertEquals('SimpleEntity', $actual);
    }

    public function test_convertFileNameToClassPath_returnsCorrectClassPath()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'convertFileNameToClassPath');
        $actual = $method->invoke($this->worker, $this->fileObj);
        $this->assertEquals('tests\MockMaker\Entities\SimpleEntity', $actual);
    }

    public function test_getClassNamespace_returnsCorrectNamespace()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getClassNamespace');
        $actual = $method->invoke($this->worker, $this->fileObj);
        $expected = 'MockMaker\Entities';
        $this->assertEquals($expected, $actual);
    }

    public function _test_generateNewObject_returnsCorrectUseStatements()
    {
        $expected = array(
            'MockMaker\Entities\TestEntity',
            'MockMaker\Entities\PropertyWorkerEntity',
        );
        $actual = $this->worker->generateNewObject($this->fileObj);
        $this->assertEquals($expected, $actual->getUseStatements());
    }

}

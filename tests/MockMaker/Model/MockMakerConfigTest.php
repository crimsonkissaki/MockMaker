<?php

/**
 * 	MockMakerConfigTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 24, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

use MockMaker\Model\MockMakerConfig;
use MockMaker\TestHelper;

class MockMakerConfigTest extends \PHPUnit_Framework_TestCase
{
    /* @var $config MockMakerConfig */

    public $config;
    public $pathToEntities;
    public $pathToGeneratedEntities;

    public function setUp()
    {
        $this->config = new MockMakerConfig();
        $this->pathToEntities = dirname(dirname(__FILE__)) . '/Entities/';
        //$this->pathToGeneratedEntities = dirname(dirname(__FILE__)) . '/Resources/GeneratedEntities/';
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
            $this->pathToEntities . 'PropertyWorkerEntity.php',
            $this->pathToEntities . 'TestEntity.php',
        );
        $this->config->addFilesToMock($files);

        $this->assertEquals(4, count($this->config->getFilesToMock()));
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

}

<?php

/**
 * 	DirectoryWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 26, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\DirectoryWorker;
use MockMaker\Model\MockMakerConfig;
use MockMaker\Exception\MockMakerException;

class DirectoryWorkerTest extends \PHPUnit_Framework_TestCase
{
    /* @var $worker DirectoryWorker */

    public $worker;
    /* @var $worker MockMakerConfig */
    public $config;
    public $entitiesDir;

    public function setUp()
    {
        $this->worker = new DirectoryWorker();
        $this->config = new MockMakerConfig();
        $this->rootDir = dirname(dirname(dirname(dirname(__FILE__))));
        $this->entitiesDir = $this->rootDir . '/tests/MockMaker/Entities/';
    }

    /**
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_validateReadDirs_throwsExceptionForInvalidDir()
    {
        $this->worker->validateReadDirs(array( 'invalidDir' ));
    }

    public function test_validateReadDirs_returnsTrueForNoDirectories()
    {
        $this->assertTrue($this->worker->validateReadDirs([ ]));
    }

    public function test_validateReadDirs_returnsTrueForValidDirectory()
    {
        $dirs = array( $this->entitiesDir );
        $actual = $this->worker->validateReadDirs($dirs);
        $this->assertTrue($actual);
    }

    public function test_validateReadDirs_returnsTrueForValidArrayOfDirectories()
    {
        $dirs = array(
            $this->entitiesDir,
            $this->entitiesDir . 'SubEntities/',
        );
        $actual = $this->worker->validateReadDirs($dirs);
        $this->assertTrue($actual);
    }

}

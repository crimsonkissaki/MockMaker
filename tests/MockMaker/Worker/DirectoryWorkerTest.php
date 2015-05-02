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
use MockMaker\Model\ConfigData;
use MockMaker\Exception\MockMakerException;

class DirectoryWorkerTest extends \PHPUnit_Framework_TestCase
{
    /* @var $worker DirectoryWorker */

    public $worker;
    /* @var $worker ConfigData */
    public $config;
    public $entitiesDir;

    public function setUp()
    {
        $this->worker = new DirectoryWorker();
        $this->config = new ConfigData();
        $this->rootDir = dirname(dirname(dirname(dirname(__FILE__))));
        $this->entitiesDir = $this->rootDir . '/tests/MockMaker/Entities/';
    }

        public function test_getAllFilesFromReadDirectories()
    {
        $actual = $this->worker->getAllFilesFromReadDirectories(array( $this->entitiesDir ));

        $this->assertEquals(7, count($actual));
    }

    public function test_getAllFilesFromReadDirectories_recursively()
    {
        $actual = $this->worker->getAllFilesFromReadDirectories(array( $this->entitiesDir ), true);

        $this->assertEquals(9, count($actual));
    }

    public function test_getAllFilesFromReadDirectories_withMultipleDirectories()
    {
        $dirsArr = array(
            $this->entitiesDir,
            $this->entitiesDir . 'SubEntities',
        );
        $actual = $this->worker->getAllFilesFromReadDirectories($dirsArr);

        $this->assertEquals(9, count($actual));
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

    public function test_guessProjectRootPath_returnsCorrectProjectRootPath()
    {
        $actual = $this->worker->guessProjectRootPath();
        $this->assertEquals('/Applications/XAMPP/xamppfiles/htdocs/mockmaker/',
            $actual);
    }

}

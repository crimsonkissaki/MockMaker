<?php

/**
 * 	FileNameWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 26, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\FileWorker;
use MockMaker\Model\ConfigData;
use MockMaker\Exception\MockMakerException;
use MockMaker\Helper\TestHelper;

class FileNameWorkerTest extends \PHPUnit_Framework_TestCase
{
    /* @var $worker FileWorker */

    public $worker;
    /* @var $worker ConfigData */
    public $config;
    /* @var $entitiesDir string */
    public $entitiesDir;
    /* @var testFilesArr array */
    public $testFilesArr = array(
        'FakeEntity.php',
        'FakeEntityRepository.php',
        'TestEntity.php',
        'TestEntityRepository.php',
        'Customer.php',
        'CustomRepository.php',
        'User.php',
        'UserRepository.php',
        'CustomerEntity.php',
        'CustomerEntityRepository.php',
        'this_is_a_strange_file.php',
        'how_did_this_get_here.yml',
        'someone_is_screwing_around.css',
        'time_to_run_git_blame.xml',
        'seriouslyWhoPutThisHere.txt',
    );

    public function setUp()
    {
        $this->worker = new FileWorker();
        $this->config = new ConfigData();
        $this->rootDir = dirname(dirname(dirname(dirname(__FILE__))));
        $this->entitiesDir = $this->rootDir . '/tests/MockMaker/Entities/';
    }

   /**
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_validateFiles_throwsExceptionOnInvalidFile()
    {
        $badFile = array( $this->entitiesDir . 'badFile.php' );
        $this->worker->validateFiles($badFile);
    }

    public function test_validateFiles_retunsTrueForValidFile()
    {
        $goodFile = array( $this->entitiesDir . 'SimpleEntity.php' );
        $actual = $this->worker->validateFiles($goodFile);
        $this->assertTrue($actual);
    }

    public function test_filterFilesWithRegex_returnsCorrectFilesWithOnlyIncludeRegex()
    {
        $files = array(
            $this->entitiesDir . 'SubEntities/SimpleSubEntity.php',
            $this->entitiesDir . 'SubEntities/SimpleSubEntityUnderscore.php',
        );
        $this->config->addFilesToAllDetectedFiles($files);
        $regex = '/Entity$/';
        $this->config->setIncludeFileRegex($regex);
        $actual = $this->worker->filterFilesWithRegex($this->config);
        $expected = array( $this->entitiesDir . 'SubEntities/SimpleSubEntity.php' );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException MockMaker\Exception\MockMakerException
     */
    public function test_getFilesThatMatchRegex_throwsExceptionForInvalidRegex()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getFilesThatMatchRegex');
        $regex = 'blatantly invalid regex';
        $method->invoke($this->worker, $this->testFilesArr, $regex);
    }

    public function test_getIncludeFiles_returnsAllFilesIfNoRegexProvided()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getIncludeFiles');
        $actual = $method->invoke($this->worker, $this->testFilesArr, '');
        $this->assertEquals($this->testFilesArr, $actual);
    }

    public function test_getIncludeFiles_returnsCorrectFilesForRegex()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getIncludeFiles');
        $regex = '/Entity$/';
        $actual = $method->invoke($this->worker, $this->testFilesArr, $regex);
        $expected = array(
            'FakeEntity.php',
            'TestEntity.php',
            'CustomerEntity.php',
        );
        $this->assertEquals($expected, $actual);
    }

    public function test_getExcludeFiles_returnsNoFilesIfNoRegexProvided()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getExcludeFiles');
        $actual = $method->invoke($this->worker, $this->testFilesArr, '');
        $this->assertEmpty($actual);
    }

    public function test_getExcludeFiles_returnsCorrectFilesForRegex()
    {
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'getExcludeFiles');
        $regex = '/Repository$/';
        $actual = $method->invoke($this->worker, $this->testFilesArr, $regex);
        $expected = array(
            'FakeEntityRepository.php',
            'TestEntityRepository.php',
            'CustomRepository.php',
            'UserRepository.php',
            'CustomerEntityRepository.php',
        );
        $this->assertEquals($expected, $actual);
    }

}

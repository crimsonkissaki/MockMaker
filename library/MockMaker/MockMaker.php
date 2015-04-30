<?php

/**
 * MockMaker
 *
 * Simple generation of seeder mock files for entities and classes
 *
 * @package     MockMaker
 * @author		Evan Johnson
 * @created	    Apr 24, 2015
 * @version     1.0
 */

namespace MockMaker;

require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use MockMaker\Model\ConfigData;
use MockMaker\Worker\DirectoryWorker;
use MockMaker\Worker\FileNameWorker;
use MockMaker\Worker\FileProcessorWorker;
use MockMaker\TestHelper;

class MockMaker
{

    /**
     * MockMaker configuration class
     *
     * @var ConfigData
     */
    private $config;

    /**
     * Class that handles directory operations
     *
     * @var DirectoryWorker
     */
    private $dirWorker;

    /**
     * Class that handles file name operations
     *
     * @var FileWorker
     */
    private $fileNameWorker;

    /**
     * Class that handles actual processing of each target file
     *
     * @var FileProcessorWorker
     */
    private $fileProcessorWorker;

    /**
     * Get the configuration options class
     *
     * @return  ConfigData
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Create a new MockMaker instance
     *
     * @return	MockMaker
     */
    public function __construct()
    {
        $this->config = new ConfigData();
        $this->dirWorker = new DirectoryWorker();
        $this->fileNameWorker = new FileNameWorker();
        $this->fileProcessorWorker = new FileProcessorWorker();
    }

    /**
     * Sets the project's root directory path
     *
     * This can be set manually if MockMaker's best guess for root path is wrong
     *
     * @param	string      $projectRootPath   Project root directory path
     * @return	MockMaker
     */
    public function setProjectRootPath($projectRootPath)
    {
        $this->config->setProjectRootPath($projectRootPath);

        return $this;
    }

    /**
     * Adds file(s) to the mocking list
     *
     * Files specified here will be merged with files found in the
     * read directory specified in the getFilesFrom() method
     *
     * @param	string|array    $files	File names to parse
     * @return	MockMaker
     */
    public function mockFiles($files)
    {
        $this->config->addFilesToAllDetectedFiles($files);

        return $this;
    }

    /**
     * Sets directories to scan for files to mock
     *
     * Any files returned from these directories will be merged with files
     * specified through the mockFiles() method
     *
     * @param	string|array    $readDirectory	Directories to scan
     * @return	MockMaker
     */
    public function getFilesFrom($readDirectory)
    {
        $this->config->addReadDirectories($readDirectory);

        return $this;
    }

    /**
     * Tells MockMaker to recursively parse read directories
     *
     * This overrides the default MockMaker setting of false
     *
     * @return	MockMaker
     */
    public function recursively()
    {
        $this->config->setRecursiveRead(true);

        return $this;
    }

    /**
     * Sets a directory to save generated mock files in
     *
     * If this is not specified MockMaker will return the generated
     * code as a string.
     *
     * @param	string  	$writeDirectory     Directory to save output files in
     * @return	MockMaker
     */
    public function saveFilesTo($writeDirectory)
    {
        $this->config->setWriteDirectory($writeDirectory);

        return $this;
    }

    /**
     * Tells MockMaker to ignore read directory's file structure and
     * save all generated files into the same directory
     *
     * Default MockMaker setting is TRUE
     *
     * @return	MockMaker
     */
    public function ignoreDirectoryStructure()
    {
        $this->config->setPreserveDirectoryStructure(false);

        return $this;
    }

    /**
     * Tells MockMaker to overwrite existing mock files
     *
     * Default MockMaker setting is FALSE
     *
     * @return	MockMaker
     */
    public function overwriteExistingFiles()
    {
        $this->config->setOverwriteExistingFiles(true);

        return $this;
    }

    /**
     * Sets a regex pattern used to EXCLUDE files
     *
     * Pattern is compared against the file name, sans extension.
     *
     * @param	string      $excludeRegex   Regex pattern
     * @return	MockMaker
     */
    public function excludeFilesWithFormat($excludeRegex)
    {
        $this->config->setExcludeFileRegex($excludeRegex);

        return $this;
    }

    /**
     * Sets a regex pattern used to INCLUDE files
     *
     * Pattern is compared against the file name, sans extension.
     *
     * @param	string      $includeRegex	Regex pattern
     * @return	MockMaker
     */
    public function includeFilesWithFormat($includeRegex)
    {
        $this->config->setIncludeFileRegex($includeRegex);

        return $this;
    }

    /**
     * NOT YET IMPLEMENTED
     *
     * Tells MockMaker to generate basic unit tests for the mock files
     *
     * Generated unit test files will only verify that the mock is returning
     * a valid instance of the mocked class.
     *
     * Default MockMaker setting is FALSE
     *
     * @return	MockMaker
     */
    public function generateMockUnitTests()
    {
        return $this;
    }

    /**
     * NOT YET IMPLEMENTED
     *
     * Sets the directory where unit test files will be saved
     *
     * If no directory has been specified the unit test code will be returned
     * with the mock file code.
     *
     * @param   string      $unitTestDirectory  Directory to save unit test files in
     * @return  MockMaker
     */
    public function saveUnitTestsTo($unitTestDirectory)
    {
        return $this;
    }

    /**
     * Returns the MockMaker config settings
     *
     * @return	ConfigData
     */
    public function verifySettings()
    {
        $this->performFinalSetup();

        return $this->config;
    }

    /**
     * Tests (in|ex)clude regex patterns
     *
     * The regex patterns will be tested against any files found in the
     * read directories or specified manually and the results returned in
     * an associative array of [include],[exclude],[workable] files.
     *
     * @return	array
     */
    public function testRegexPatterns()
    {
        $this->getAllPossibleFilesToBeWorked();

        return $this->fileNameWorker->testRegexPatterns($this->config);
    }

    /**
     * Generates mocks for any valid target files
     *
     * If a write directory has been specified, the code will be saved
     * there, otherwise it will be returned as a string.
     *
     * -- WARNING -- Abstract/Interface classes cannot be mocked!
     *
     * @return  string
     */
    public function createMocks()
    {
        $this->performFinalSetup();
        if ($this->config->getWriteDirectory()) {
            $this->dirWorker->validateWriteDir($this->config->getWriteDirectory());
        }
        $this->fileProcessorWorker->setConfig($this->config);
        $code = $this->fileProcessorWorker->processFiles();

        return $code;
    }

    /**
     * Performs final set up required before processing
     *
     * @return  bool
     */
    private function performFinalSetup()
    {
        $this->determineWorkableFiles();
        if (!$this->config->getProjectRootPath()) {
            $this->config->setProjectRootPath($this->dirWorker->guessProjectRootPath());
        }

        return true;
    }

    /**
     * Combines all files from the read directories with the user specified ones
     *
     * @return  array
     */
    private function getAllPossibleFilesToBeWorked()
    {
        $dirFiles = $this->getListOfFilesFromReadDir();
        $this->config->addFilesToAllDetectedFiles($dirFiles);

        return $this->config->getAllDetectedFiles();
    }

    /**
     * Scans the specified directories for any files
     *
     * @return  array
     */
    private function getListOfFilesFromReadDir()
    {
        $allFiles = [ ];
        $readDirs = $this->config->getReadDirectories();
        if (!empty($readDirs)) {
            $this->dirWorker->validateReadDirs($readDirs);
            $allFiles = $this->fileNameWorker->getAllFilesFromReadDirectories($readDirs,
                $this->config->getRecursiveRead());
        }

        return $allFiles;
    }

    /**
     * Returns files that need to be processed
     *
     * @return  array
     */
    private function determineWorkableFiles()
    {
        $this->getAllPossibleFilesToBeWorked();
        $workableFiles = $this->fileNameWorker->filterFilesWithRegex($this->config);
        if ($this->fileNameWorker->validateFiles($workableFiles)) {
            $this->config->setFilesToMock($workableFiles);
        }

        return $workableFiles;
    }

}

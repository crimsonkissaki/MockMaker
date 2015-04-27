<?php

/**
 * 	MockMaker
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 24, 2015
 * 	@version	1.0
 */

namespace MockMaker;

require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use MockMaker\Model\MockMakerConfig;
use MockMaker\Worker\DirectoryWorker;
use MockMaker\Worker\FileWorker;

class MockMaker
{

    /**
     * MockMaker configuration class.
     *
     * @var MockMakerConfig
     */
    private $config;

    /**
     * Class that handles directory operations.
     *
     * @var DirectoryWorker
     */
    private $dirWorker;

    /**
     * Class that handles file operations.
     *
     * @var FileWorker
     */
    private $fileWorker;

    /**
     * Get the configuration options class
     *
     * @return  MockMakerConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the directory worker class
     *
     * @return  DirectoryWorker
     */
    public function getDirWorker()
    {
        return $this->dirWorker;
    }

    /**
     * Get the file worker class
     *
     * @return  FileWorker
     */
    public function getFileWorker()
    {
        return $this->fileWorker;
    }

    /**
     * Create a new MockMaker instance.
     *
     * @return	MockMaker
     */
    public function __construct()
    {
        $this->config = new MockMakerConfig();
        $this->dirWorker = new DirectoryWorker();
        $this->fileWorker = new FileWorker();
    }

    /**
     * Set your project's root directory path.
     *
     * MockMaker will do its best to guess this based on class namespaces,
     * but you can set it manually if it's having problems.
     *
     * @param	$rootDir	string	Project root directory.
     * @return	MockMaker
     */
    public function setRootDirectory($rootDir)
    {
        $this->config->setRootDirectory($rootDir);

        return $this;
    }

    /**
     * Add one or an array of files to the list of files to be generated.
     *
     * Any files specified here will be merged with files found in the
     * read directory specified in the getFilesFrom() method.
     *
     * @param	$files	mixed	Single or array of file names to parse.
     * @return	MockMaker
     */
    public function mockFiles($files)
    {
        $this->config->addFilesToMock($files);

        return $this;
    }

    /**
     * Set a single or array of directories to scan for files to mock.
     *
     * Any files returned from these directories will be merged with files
     * specified through the mockFiles() method.
     *
     * @param	$readDirectory	mixed	Single or array of directories to scan.
     * @return	MockMaker
     */
    public function getFilesFrom($readDirectory)
    {
        $this->config->addReadDirectories($readDirectory);

        return $this;
    }

    /**
     * Parse the read directory recursively.
     *
     * Default MockMaker setting is FALSE.
     *
     * @param	$recursively	bool	Default true.
     * @return	MockMaker
     */
    public function recursively($recursively = true)
    {
        $this->config->setRecursiveRead($recursively);

        return $this;
    }

    /**
     * Set a directory to save generated mock files in.
     *
     * If you do not specify a write directory, MockMaker will return
     * the generated code as a string.
     *
     * @param	$writeDirectory	string	Directory to save output files in.
     * @return	MockMaker
     */
    public function saveFilesTo($writeDirectory)
    {
        $this->config->setWriteDirectory($writeDirectory);

        return $this;
    }

    /**
     * Tell MockMaker to ignore the read directory's file structure and
     * save all generated files into the same directory.
     *
     * Default MockMaker setting is TRUE.
     *
     * @return	MockMaker
     */
    public function ignoreDirectoryStructure()
    {
        $this->config->setPreserveDirectoryStructure(false);

        return $this;
    }

    /**
     * Set whether or not to overwrite existing files.
     *
     * Default MockMaker setting is FALSE.
     *
     * @param	$overwriteExistingFiles		bool
     * @return	MockMaker
     */
    public function overwriteExistingFiles()
    {
        $this->config->setOverwriteExistingFiles(true);

        return $this;
    }

    /**
     * Define a regex pattern used to EXCLUDE files.
     *
     * This is only used when a read directory has been specified.
     *
     * @param	$excludeRegex   string	Regex pattern.
     * @return	MockMaker
     */
    public function excludeFilesWithFormat($excludeRegex)
    {
        $this->config->setExcludeFileRegex($excludeRegex);

        return $this;
    }

    /**
     * Define a regex pattern used to INCLUDE files.
     *
     * This is only used when a read directory has been specified.
     *
     * @param	$includeRegex	string	Regex pattern.
     * @return	MockMaker
     */
    public function includeFilesWithFormat($includeRegex)
    {
        $this->config->setIncludeFileRegex($includeRegex);

        return $this;
    }

    /**
     * Use to verify MockMaker's settings before you kick things off for real.
     *
     * @return	MockMakerConfig
     */
    public function verifySettings()
    {
        $this->validateDirectories();
        $this->validateFiles();
        return $this->config;
    }

    /**
     * Test (in|ex)clude regex patterns against a specified read directory.
     *
     * TODO: finish this
     *
     * @return	array
     */
    public function testRegexPatterns()
    {
        /**
         * To test the regex patterns, we will already need to have the directories
         * validated and approved.
         * We then have to execute the "getFilesFromReadDirectories()" method
         * and have the regex executed based on that.
         */
        return $this->fileWorker->testRegexPatterns($this->config);
    }

    /**
     * Git 'er done!
     */
    public function execute()
    {
        // are directories valid?
        // add all files from directories to filesToMock[]
        // are files valid?
    }

    /**
     * Scan the provided read directories and get files to mock.
     *
     * @return  array
     */
    private function getFilesFromReadDirectories()
    {
        $files = $this->fileWorker->getFilesFromReadDirectories($this->config);
        $this->config->addFilesToMock($files);

        return $files;
    }

    /**
     * Ensure all specified directories exist and have correct permissions.
     *
     * @throws  MockMakerException
     * @return  bool
     */
    private function validateDirectories()
    {
        return $this->dirWorker->validateDirectories($this->config);
    }

    /**
     * Ensure all specified files exist and have correct permissions.
     *
     * @throws  MockMakerException
     * @return  bool
     */
    private function validateFiles()
    {
        return $this->fileWorker->validateFiles($this->config);
    }

}

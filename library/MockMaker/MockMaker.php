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
use MockMaker\TestHelper;

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
        $this->config->addFilesToAllDetectedFiles($files);

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
     * Define a regex pattern used to EXCLUDE files based on the file name.
     *
     * YOU DO NOT NEED TO INCLUDE THE FILE EXTENSION IN THE REGEX STRING
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
     * Define a regex pattern used to INCLUDE files based on the file name.
     *
     * YOU DO NOT NEED TO INCLUDE THE FILE EXTENSION IN THE REGEX STRING
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
        $this->determineWorkableFiles();

        return $this->config;
    }

    /**
     * Test (in|ex)clude regex patterns against any spefified files or
     * files found in specified read directories.
     *
     * Returns an associative array of [include],[exclude],[workable] files.
     *
     * @return	array
     */
    public function testRegexPatterns()
    {
        $this->getAllPossibleFilesToBeWorked();

        return $this->fileWorker->testRegexPatterns($this->config);
    }

    /**
     * Git 'er done!
     */
    public function execute()
    {
        $this->determineWorkableFiles();
        if ($this->config->getWriteDirectory()) {
            $this->dirWorker->validateWriteDir($this->config->getWriteDirectory());
        }
        // process files
        // $code = $this->processAllFiles();
    }

    /**
     * Get every single file that's user specified or in the read directories.
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
     * Scan the specified directories for any files.
     *
     * @return  array
     */
    private function getListOfFilesFromReadDir()
    {
        $allFiles = [ ];
        $readDirs = $this->config->getReadDirectories();
        if (!empty($readDirs)) {
            $this->dirWorker->validateReadDirs($readDirs);
            $allFiles = $this->fileWorker->getAllFilesFromReadDirectories($readDirs, $this->config->getRecursiveRead());
        }

        return $allFiles;
    }

    /**
     * Filter and validate the allDetectedFiles() array and return the
     * files we need to actually process.
     *
     * @return  array
     */
    private function determineWorkableFiles()
    {
        $this->getAllPossibleFilesToBeWorked();
        $workableFiles = $this->fileWorker->filterFilesWithRegex($this->config);
        $this->fileWorker->validateFiles($workableFiles);
        $this->config->setFilesToMock($workableFiles);

        return $workableFiles;
    }

}

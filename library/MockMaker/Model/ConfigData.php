<?php

/**
 * ConfigData
 *
 * MockMaker configuration data class
 *
 * This class ONLY holds data that the user has supplied through MockMaker options.
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 22, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

use MockMaker\Exception\MockMakerErrors;
use MockMaker\Exception\MockMakerException;
use MockMaker\Exception\MockMakerFatalException;
use MockMaker\Worker\AbstractDataPointWorker;
use MockMaker\Worker\DefaultMockDataPointWorker;
use MockMaker\Worker\DefaultUnitTestDataPointWorker;
use MockMaker\Worker\PathWorker as Formatter;
use MockMaker\Worker\DirectoryWorker;
use MockMaker\Worker\FileWorker;

class ConfigData
{

    /**
     * Project root directory path
     *
     * @var    string
     */
    private $projectRootPath;

    /**
     * Read the directory recursively or not
     *
     * @var    bool
     */
    private $recursiveRead = false;

    /**
     * Overwrite existing mock files or not
     *
     * @var    bool
     */
    private $overwriteMockFiles = false;

    /**
     * Overwrite existing unit test files or not
     *
     * @var    bool
     */
    private $overwriteUnitTestFiles = false;

    /**
     * Mimic the read directory file tree in the write directory or not
     *
     * @var    bool
     */
    private $preserveDirStructure = true;

    /**
     * Directories to scan for files to mock
     *
     * @var    array
     */
    private $readDirectories = [];

    /**
     * Regex pattern used to exclude files
     *
     * @var    string
     */
    private $excludeFileRegex;

    /**
     * Regex pattern uses to include files
     *
     * @var    string
     */
    private $includeFileRegex;

    /**
     * Directory to write generated mock files
     *
     * @var    string
     */
    private $mockWriteDir;

    /**
     * Directory to write generated mock unit test files
     *
     * @var    string
     */
    private $unitTestWriteDir;

    /**
     * Template for mock file names
     *
     * @var string;
     */
    private $mockFileNameFormat = '%FileName%Mock';

    /**
     * The base namespace used for mock files
     *
     * @var string
     */
    private $mockFileBaseNamespace;

    /**
     * All files indicated by user or in read directories
     *
     * @var    array
     */
    private $allDetectedFiles = [];

    /**
     * Array of files to generate mocks for
     *
     * @var    array
     */
    private $filesToMock = [];

    /**
     * Class that transforms DataFile objects into mock code
     *
     * @var AbstractDataPointWorker
     */
    private $mockDataPointWorker;

    /**
     * Class that transforms DataFile objects into mock unit test code
     *
     * @var AbstractDataPointWorker
     */
    private $utDataPointWorker;

    /**
     * Array of DataPointWorkers that need to process
     *
     * @var array
     */
    private $dataPointWorkers = [];

    /**
     * Gets if the read directory should be recursively scanned
     *
     * @return    bool
     */
    public function getRecursiveRead()
    {
        return $this->recursiveRead;
    }

    /**
     * Gets if mock files are to be overwritten
     *
     * @return    bool
     */
    public function getOverwriteMockFiles()
    {
        return $this->overwriteMockFiles;
    }

    /**
     * Gets if unit test files are to be overwritten
     *
     * @return    bool
     */
    public function getOverwriteUnitTestFiles()
    {
        return $this->overwriteUnitTestFiles;
    }

    /**
     * Gets directory names to scan for files
     *
     * @return    string
     */
    public function getReadDirectories()
    {
        return $this->readDirectories;
    }

    /**
     * Gets directory name to save generated mock files
     *
     * @return    string
     */
    public function getMockWriteDir()
    {
        return $this->mockWriteDir;
    }

    /**
     * Gets all files indicated by user or in read directories
     *
     * @return    array
     */
    public function getAllDetectedFiles()
    {
        return $this->allDetectedFiles;
    }

    /**
     * Gets array of files to be mocked
     *
     * @return    array
     */
    public function getFilesToMock()
    {
        return $this->filesToMock;
    }

    /**
     * Gets exclude file regex string
     *
     * @return    string
     */
    public function getExcludeFileRegex()
    {
        return $this->excludeFileRegex;
    }

    /**
     * Gets include file regex string
     *
     * @return    string
     */
    public function getIncludeFileRegex()
    {
        return $this->includeFileRegex;
    }

    /**
     * Gets if read directory file structure should be used in write directory
     *
     * @return    bool
     */
    public function getPreserveDirStructure()
    {
        return $this->preserveDirStructure;
    }

    /**
     * Gets project's root directory path
     *
     * @return    string
     */
    public function getProjectRootPath()
    {
        return $this->projectRootPath;
    }

    /**
     * Gets the file format for mock file names
     *
     * @return string
     */
    public function getMockFileNameFormat()
    {
        return $this->mockFileNameFormat;
    }

    /**
     * Gets the base namespace to be used in generated mocks
     *
     * @return string
     */
    public function getMockFileBaseNamespace()
    {
        return $this->mockFileBaseNamespace;
    }

    /**
     * Gets the directory to save mock unit tests in
     *
     * @return string
     */
    public function getUnitTestWriteDir()
    {
        return $this->unitTestWriteDir;
    }

    /**
     * Get the mock file DataPointWorker class
     *
     * If no custom class has been defined, the default class
     * will be the DefaultMockDataPointWorker class.
     *
     * @return AbstractDataPointWorker
     */
    public function getMockDataPointWorker()
    {
        if (!$this->mockDataPointWorker) {
            $this->setMockDataPointWorker(new DefaultMockDataPointWorker());
        }

        return $this->mockDataPointWorker;
    }

    /**
     * Gets the unit test DataPointWorker class
     *
     * @return AbstractDataPointWorker
     */
    public function getUtDataPointWorker()
    {
        if (!$this->utDataPointWorker) {
            $this->setUtDataPointWorker(new DefaultUnitTestDataPointWorker());
        }

        return $this->utDataPointWorker;
    }

    /**
     * Gets the DataPointWorkers that need to be executed
     *
     * TODO: allow overriding/registering of custom workers
     *
     * @return array
     */
    public function getDataPointWorkers()
    {
        $dpw = array(
            new DefaultMockDataPointWorker(),
            new DefaultUnitTestDataPointWorker(),
        );

        return $dpw;
    }

    /**
     * Sets if read directory is to be scanned recursively
     *
     * @param    bool $recursiveRead Parse read directory recursively
     */
    public function setRecursiveRead($recursiveRead)
    {
        $this->recursiveRead = $recursiveRead;
    }

    /**
     * Sets if existing files are to be overwritten
     *
     * @param    bool $overwriteMockFiles Overwrite existing mock files
     */
    public function setOverwriteMockFiles($overwriteMockFiles)
    {
        $this->overwriteMockFiles = $overwriteMockFiles;
    }

    /**
     * Sets if existing files are to be overwritten
     *
     * @param    bool $overwriteUnitTestFiles Overwrite existing unit test files
     */
    public function setOverwriteUnitTestFiles($overwriteUnitTestFiles)
    {
        $this->overwriteUnitTestFiles = $overwriteUnitTestFiles;
    }

    /**
     * Sets directories to scan for files to mock
     *
     * @param   array $readDirectories Directories to scan
     */
    public function setReadDirectories(array $readDirectories)
    {
        DirectoryWorker::validateReadDirs($readDirectories);
        $this->readDirectories = Formatter::formatDirectoryPaths($readDirectories);
    }

    /**
     * Adds (single|array of) directories to parse for files
     *
     * @param    string|array $readDirectories Directories to scan for files to mock
     */
    public function addReadDirectories($readDirectories)
    {
        $dirs = (is_array($readDirectories)) ? $readDirectories : array($readDirectories);
        $formattedDirs = Formatter::formatDirectoryPaths($dirs);
        DirectoryWorker::validateReadDirs($formattedDirs);
        $this->setReadDirectories(array_merge($this->readDirectories, $formattedDirs));
    }

    /**
     * Sets directory name to save generated mock files in
     *
     * @param   string $mockWriteDir Directory to save mock files in
     */
    public function setMockWriteDir($mockWriteDir)
    {
        DirectoryWorker::validateWriteDir($mockWriteDir);
        $this->mockWriteDir = Formatter::formatDirectoryPath($mockWriteDir);
    }

    /**
     * Sets files indicated by user or in read directories
     *
     * @param   array $allDetectedFiles File(s) detected as possible mocking candidates
     */
    public function setAllDetectedFiles(array $allDetectedFiles)
    {
        $this->allDetectedFiles = $allDetectedFiles;
    }

    /**
     * Adds files to to the detected file list
     *
     * @param    string|array $files File(s) to add to allDetectedFiles
     */
    public function addToAllDetectedFiles($files)
    {
        if (is_array($files)) {
            $this->setAllDetectedFiles(array_merge($this->allDetectedFiles, $files));
        } else {
            array_push($this->allDetectedFiles, $files);
        }
    }

    /**
     * Sets the files to generate mocks for
     *
     * @param    string|array $filesToMock Files to be mocked
     */
    public function setFilesToMock(array $filesToMock)
    {
        FileWorker::validateFiles($filesToMock);
        $this->filesToMock = $filesToMock;
    }

    /**
     * Adds files to the list of files to be mocked
     *
     * @param   string|array $files File(s) to add to list of files to be mocked
     */
    public function addFilesToMock($files)
    {
        if (is_array($files)) {
            $this->setFilesToMock(array_merge($this->filesToMock, $files));
        } else {
            array_push($this->filesToMock, $files);
        }
    }

    /**
     * Sets the ignore file filter regex string
     *
     * @param    $excludeFileRegex   string    Regex string used to exclude files
     */
    public function setExcludeFileRegex($excludeFileRegex)
    {
        $this->excludeFileRegex = trim($excludeFileRegex);
    }

    /**
     * Sets the include file regex string
     *
     * @param    $includeFileRegex    string    Regex string used to include files
     */
    public function setIncludeFileRegex($includeFileRegex)
    {
        $this->includeFileRegex = trim($includeFileRegex);
    }

    /**
     * Sets whether to mimic read directory file structure in write directory
     *
     * @param    $preserveDirStructure    bool    Mirror read directory structure in write directory
     */
    public function setPreserveDirStructure($preserveDirStructure)
    {
        $this->preserveDirStructure = $preserveDirStructure;
    }

    /**
     * Sets the project's root directory path
     *
     * Validates path before setting it.
     *
     * @param    $projectRootPath    string    Path to your project's root directory
     */
    public function setProjectRootPath($projectRootPath)
    {
        $path = Formatter::formatDirectoryPath($projectRootPath);
        DirectoryWorker::checkIsValidDirectory($path, MockMakerErrors::INVALID_PROJECT_ROOT_PATH);
        $this->projectRootPath = $path;
    }

    /**
     * Sets the base namespace to be used when creating mock files
     *
     * If this is not set, MockMaker will make a best-guess.
     *
     * @param string $mockFileBaseNamespace
     */
    public function setMockFileBaseNamespace($mockFileBaseNamespace)
    {
        $this->mockFileBaseNamespace = $mockFileBaseNamespace;
    }

    /**
     * Sets the directory to save mock unit tests in
     *
     * @param   string $unitTestWriteDir
     */
    public function setUnitTestWriteDir($unitTestWriteDir)
    {
        DirectoryWorker::validateWriteDir($unitTestWriteDir);
        $this->unitTestWriteDir = $unitTestWriteDir;
    }

    /**
     * Sets the base mock file name format used when creating mock files
     *
     * @param string $mockFileNameFormat
     */
    public function setMockFileNameFormat($mockFileNameFormat)
    {
        $this->mockFileNameFormat = $mockFileNameFormat;
    }

    /**
     * Set a custom class to process the mock code.
     *
     * @param   AbstractDataPointWorker $mockDataPointWorker
     */
    public function setMockDataPointWorker(AbstractDataPointWorker $mockDataPointWorker)
    {
        $this->mockDataPointWorker = $mockDataPointWorker;
    }

    /**
     * Sets the unit test DataPointWorker class
     *
     * @param AbstractDataPointWorker $utDataPointWorker
     */
    public function setUtDataPointWorker($utDataPointWorker)
    {
        $this->utDataPointWorker = $utDataPointWorker;
    }

    /**
     * Adds a new DataPointWorker to the worker queue
     *
     * @param AbstractDataPointWorker $dataPointWorker
     */
    public function registerDataPointWorker(AbstractDataPointWorker $dataPointWorker)
    {
        array_push($this->dataPointWorkers, $dataPointWorker);
    }
}

<?php

/**
 * ConfigData
 *
 * MockMaker configuration data class
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 22, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

use MockMaker\Worker\StringFormatterWorker as Formatter;
use MockMaker\Worker\AbstractCodeWorker;
use MockMaker\Worker\CodeWorker;
use MockMaker\Worker\DirectoryWorker;
use MockMaker\Worker\FileWorker;

class ConfigData
{

    /**
     * Read the directory recursively or not
     *
     * @var    bool
     */
    private $recursiveRead = false;

    /**
     * Overwrite existing files or not
     *
     * @var    bool
     */
    private $overwriteExistingFiles = false;

    /**
     * Directories to scan for files to mock
     *
     * @var    array
     */
    private $readDirectories = [];

    /**
     * Directory to write generated mock files
     *
     * @var    string
     */
    private $mockWriteDirectory;

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
     * Mimick the read directory file tree in the write directory or not
     *
     * @var    bool
     */
    private $preserveDirectoryStructure = true;

    /**
     * Project root directory path
     *
     * @var    string
     */
    private $projectRootPath;

    /**
     * Class that transforms DataFile objects into mock code
     *
     * @var AbstractCodeWorker
     */
    private $codeWorker;

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
     * Gets if files are to be overwritten
     *
     * @return    bool
     */
    public function getOverwriteExistingFiles()
    {
        return $this->overwriteExistingFiles;
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
    public function getMockWriteDirectory()
    {
        return $this->mockWriteDirectory;
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
    public function getPreserveDirectoryStructure()
    {
        return $this->preserveDirectoryStructure;
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
     * Get the code working class
     *
     * If no custom class has been defined, the default class
     * will be the CodeWorker class.
     *
     * @return AbstractCodeWorker
     */
    public function getCodeWorker()
    {
        return ($this->codeWorker) ? $this->codeWorker : new CodeWorker();
    }

    /**
     * Sets if read directory is to be scanned recursively
     *
     * @param    bool $recursiveRead Parse read directory recursively
     * @return  void
     */
    public function setRecursiveRead($recursiveRead)
    {
        $this->recursiveRead = $recursiveRead;
    }

    /**
     * Sets if existing files are to be overwritten
     *
     * @param    bool $overwriteExistingFiles Overwrite existing files
     * @return  void
     */
    public function setOverwriteExistingFiles($overwriteExistingFiles)
    {
        $this->overwriteExistingFiles = $overwriteExistingFiles;
    }

    /**
     * Sets directories to scan for files to mock
     *
     * @param    array $readDirectories Directories to scan
     * @return  void
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
     * @return  void
     */
    public function addReadDirectories($readDirectories)
    {
        if (is_array($readDirectories)) {
            $merged = array_merge($this->readDirectories, $readDirectories);
            $this->setReadDirectories(Formatter::formatDirectoryPaths($merged));
        } else {
            array_push($this->readDirectories, Formatter::formatDirectoryPath($readDirectories));
        }
    }

    /**
     * Sets directory name to save generated mock files in
     *
     * @param    string $mockWriteDirectory Directory to save mock files in
     * @return  void
     */
    public function setMockWriteDirectory($mockWriteDirectory)
    {
        DirectoryWorker::validateWriteDir($mockWriteDirectory);
        $this->mockWriteDirectory = Formatter::formatDirectoryPath($mockWriteDirectory);
    }

    /**
     * Sets files indicated by user or in read directories
     *
     * @param   array $allDetectedFiles File(s) detected as possible mocking candidates
     * @return  void
     */
    public function setAllDetectedFiles(array $allDetectedFiles)
    {
        $this->allDetectedFiles = $allDetectedFiles;
    }

    /**
     * Adds files to to the detected file list
     *
     * @param    string|array $files File(s) to add to allDetectedFiles
     * @return  void
     */
    public function addFilesToAllDetectedFiles($files)
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
     * @return  void
     */
    public function setFilesToMock(array $filesToMock)
    {
        FileWorker::validateFiles($filesToMock);
        $this->filesToMock = $filesToMock;
    }

    /**
     * Adds files to the list of files to be mocked
     *
     * @param    string|array $files File(s) to add to list of files to be mocked
     * @return  void
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
     * @return  void
     */
    public function setExcludeFileRegex($excludeFileRegex)
    {
        $this->excludeFileRegex = trim($excludeFileRegex);
    }

    /**
     * Sets the include file regex string
     *
     * @param    $includeFileRegex    string    Regex string used to include files
     * @return  void
     */
    public function setIncludeFileRegex($includeFileRegex)
    {
        $this->includeFileRegex = trim($includeFileRegex);
    }

    /**
     * Sets whether to mimick read directory file structure in write directory
     *
     * @param    $preserveDirectoryStructure    bool    Mirror read directory structure in write directory
     * @return  void
     */
    public function setPreserveDirectoryStructure($preserveDirectoryStructure)
    {
        $this->preserveDirectoryStructure = $preserveDirectoryStructure;
    }

    /**
     * Sets the project's root directory path
     *
     * @param    $projectRootPath    string    Path to your project's root directory
     * @return  void
     */
    public function setProjectRootPath($projectRootPath)
    {
        $this->projectRootPath = Formatter::formatDirectoryPath($projectRootPath);
    }

    /**
     * Set a custom class to process the mock code.
     *
     * @param AbstractCodeWorker $codeWorker
     */
    public function setCodeWorker(AbstractCodeWorker $codeWorker)
    {
        $this->codeWorker = $codeWorker;
    }
}
<?php

/**
 * 	ConfigData
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 22, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

use MockMaker\Worker\StringFormatterWorker as Formatter;

class ConfigData
{

    /**
     * Set whether or not to read the directory recursively.
     *
     * @var	bool
     */
    private $recursiveRead = false;

    /**
     * Overwrite existing files or not?
     *
     * @var	bool
     */
    private $overwriteExistingFiles = false;

    /**
     * Directories to scan for files that need mocks generated.
     *
     * @var	array
     */
    private $readDirectories = [ ];

    /**
     * Directory to write generated mock files.
     *
     * @var	string
     */
    private $writeDirectory;

    /**
     * All files indicated by user or in read directories.
     *
     * @var	array
     */
    private $allDetectedFiles = [ ];

    /**
     * Array of files to generate mocks for.
     *
     * @var	array
     */
    private $filesToMock = [ ];

    /**
     * Regex pattern to use to exclude files from mocking.
     *
     * @var	string
     */
    private $excludeFileRegex;

    /**
     * Regex pattern to use to include files for mocking.
     *
     * @var	string
     */
    private $includeFileRegex;

    /**
     * Whether to mimick the read directory file structure
     * in the write directory.
     *
     * @var	bool
     */
    private $preserveDirectoryStructure = true;

    /**
     * The root directory path for your project.
     *
     * @var	string
     */
    private $projectRootPath;

    /**
     * Get whether to read the directory recursively.
     *
     * @return	bool
     */
    public function getRecursiveRead()
    {
        return $this->recursiveRead;
    }

    /**
     * Get whether or not to overwrite existing files.
     *
     * @return	bool
     */
    public function getOverwriteExistingFiles()
    {
        return $this->overwriteExistingFiles;
    }

    /**
     * Get directory names to scan for files to mock.
     *
     * @return	string
     */
    public function getReadDirectories()
    {
        return $this->readDirectories;
    }

    /**
     * Get directory name to save generated mock files in.
     *
     * @return	string
     */
    public function getWriteDirectory()
    {
        return $this->writeDirectory;
    }

    /**
     * Get an array all files indicated by user or in read directories.
     *
     * @return	array
     */
    public function getAllDetectedFiles()
    {
        return $this->allDetectedFiles;
    }

    /**
     * Get array of files that need mocks generated.
     *
     * @return	array
     */
    public function getFilesToMock()
    {
        return $this->filesToMock;
    }

    /**
     * Get the exclude file regex string.
     *
     * @return	string
     */
    public function getExcludeFileRegex()
    {
        return $this->excludeFileRegex;
    }

    /**
     * Get the include file regex string.
     *
     * @return	string
     */
    public function getIncludeFileRegex()
    {
        return $this->includeFileRegex;
    }

    /**
     * Get whether to mimick the read directory file structure
     * in the write directory.
     *
     * @return	bool
     */
    public function getPreserveDirectoryStructure()
    {
        return $this->preserveDirectoryStructure;
    }

    /**
     * Get the project's root directory path.
     *
     * @return	string
     */
    public function getProjectRootPath()
    {
        return $this->projectRootPath;
    }

    /**
     * Set whether to read the directory recursively.
     *
     * @param	$recursiveRead	bool
     */
    public function setRecursiveRead($recursiveRead)
    {
        $this->recursiveRead = $recursiveRead;
    }

    /**
     * Set whether or not to overwrite existing files.
     *
     * @param	$overwriteExistingFiles		bool
     */
    public function setOverwriteExistingFiles($overwriteExistingFiles)
    {
        $this->overwriteExistingFiles = $overwriteExistingFiles;
    }

    /**
     * Set directory name(s) to scan for files to mock.
     *
     * @param	$readDirectories	mixed
     */
    public function setReadDirectories($readDirectories)
    {
        $dirs = is_array($readDirectories) ? $readDirectories : array( $readDirectories );
        $this->readDirectories = Formatter::formatDirectoryPaths($dirs);
    }

    /**
     * Add a single or array of directories to parse for files.
     *
     * @param	$readDirectories	mixed	Single or array of directories.
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
     * Set directory name to save generated mock files in.
     *
     * @param	$writeDirectory	string
     */
    public function setWriteDirectory($writeDirectory)
    {
        $this->writeDirectory = Formatter::formatDirectoryPath($writeDirectory);
    }

    /**
     * Set an array all files indicated by user or in read directories.
     *
     * @param   $allDetectedFiles   array
     */
    public function setAllDetectedFiles($allDetectedFiles)
    {
        $this->allDetectedFiles = $allDetectedFiles;
    }

    /**
     * Add either a single file or an array
     * of files to the "all detected files" array.
     *
     * @param	$files	mixed
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
     * Set an array of individual files to generate mocks for.
     *
     * @param	$filesToMock	mixed
     */
    public function setFilesToMock($filesToMock)
    {
        $files = is_array($filesToMock) ? $filesToMock : array( $filesToMock );
        $this->filesToMock = $files;
    }

    /**
     * Add either a single file or an array
     * of files to the "files to mock" array.
     *
     * @param	$files	mixed
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
     * Set the ignore file filter regex string.
     *
     * @param	$excludeFileRegex   string	Regex string used to exclude files.
     */
    public function setExcludeFileRegex($excludeFileRegex)
    {
        $this->excludeFileRegex = trim($excludeFileRegex);
    }

    /**
     * Set the include file regex string.
     *
     * @param	$includeFileRegex	string	Regex string used to include files.
     */
    public function setIncludeFileRegex($includeFileRegex)
    {
        $this->includeFileRegex = trim($includeFileRegex);
    }

    /**
     * Set whether to mimick the read directory file structure
     * in the write directory.
     *
     * @param	$preserveDirectoryStructure	bool	Mirror read directory structure in write directory.
     */
    public function setPreserveDirectoryStructure($preserveDirectoryStructure)
    {
        $this->preserveDirectoryStructure = $preserveDirectoryStructure;
    }

    /**
     * Set the project's root directory path.
     *
     * @param	$projectRootPath	string	Path to your project's root directory.
     */
    public function setProjectRootPath($projectRootPath)
    {
        $this->projectRootPath = Formatter::formatDirectoryPath($projectRootPath);
    }

}

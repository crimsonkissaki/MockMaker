<?php

/**
 * MockMakerFileData
 *
 * Holds all file information we need to create the mock file and
 * will be passed to the code generator classes later on
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

use MockMaker\Model\ClassData;

class MockMakerFileData
{

    /**
     * Path to the project root directory
     *
     * @var string
     */
    private $projectRootPath;

    /**
     * Fully qualified file path & name
     *
     * @var string
     */
    private $sourceFileFullPath;

    /**
     * Simple file name, e.g. "Customer.php"
     *
     * @var string
     */
    private $sourceFileName;

    /**
     * Path to the mock file write directory
     *
     * @var string
     */
    private $mockWriteDirectory;

    /**
     * Overwrite existing files or not
     *
     * @var bool
     */
    private $overwriteExistingFiles;

    /**
     * Mock file base namespace
     *
     * @var string
     */
    private $mockFileBaseNamespace;

    /**
     * File name format for mock file
     *
     * @var string
     */
    private $mockFileNameFormat;

    /**
     * File name of mock file
     *
     * @var string
     */
    private $mockFileName;

    /**
     * Namespace of the mock file
     *
     * @var string
     */
    private $mockFileNamespace;

    /**
     * Full path to save mock file
     *
     * @var string
     */
    private $mockFileSavePath;

    /**
     * ClassData data object
     *
     * @var ClassData
     */
    private $classData;

    /**
     * Gets the file's full path
     *
     * @return string
     */
    public function getSourceFileFullPath()
    {
        return $this->sourceFileFullPath;
    }

    /**
     * Gets the file's name
     *
     * @return string
     */
    public function getSourceFileName()
    {
        return $this->sourceFileName;
    }

    /**
     * Gets the mock file name format
     *
     * @return string
     */
    public function getMockFileNameFormat()
    {
        return $this->mockFileNameFormat;
    }

    /**
     * Gets the mock file's name
     *
     * @return string
     */
    public function getMockFileName()
    {
        return $this->mockFileName;
    }

    /**
     * Gets the mock file's namespace
     *
     * @return string
     */
    public function getMockFileNamespace()
    {
        return $this->mockFileNamespace;
    }

    /**
     * Gets the path to the project root directory
     *
     * @return  string
     */
    public function getProjectRootPath()
    {
        return $this->projectRootPath;
    }

    /**
     * Gets the directory to save mock files in
     *
     * @return string
     */
    public function getMockWriteDirectory()
    {
        return $this->mockWriteDirectory;
    }

    /**
     * Gets the full path for saving the mock file
     *
     * @return string
     */
    public function getMockFileSavePath()
    {
        return $this->mockFileSavePath;
    }

    /**
     * Gets the user defined mock file base namespace
     *
     * @return string
     */
    public function getMockFileBaseNamespace()
    {
        return $this->mockFileBaseNamespace;
    }

    /**
     * Gets whether or not to overwrite existing mock files
     *
     * @return bool
     */
    public function getOverwriteExistingFiles()
    {
        return $this->overwriteExistingFiles;
    }
    /**
     * Gets the file's ClassData object
     *
     * @return ClassData
     */
    public function getClassData()
    {
        return $this->classData;
    }

    /**
     * Sets the file's full path
     *
     * @param   $sourceFileFullPath   string
     * @return  MockMakerFileData
     */
    public function setSourceFileFullPath($sourceFileFullPath)
    {
        $this->sourceFileFullPath = $sourceFileFullPath;

        return $this;
    }

    /**
     * Sets the file's name
     *
     * @param   $sourceFileName   string
     * @return  MockMakerFileData
     */
    public function setSourceFileName($sourceFileName)
    {
        $this->sourceFileName = $sourceFileName;

        return $this;
    }

    /**
     * Sets the mock file name format
     *
     * @param   string      $mockFileNameFormat
     * @return  MockMakerFileData
     */
    public function setMockFileNameFormat($mockFileNameFormat)
    {
        $this->mockFileNameFormat = $mockFileNameFormat;

        return $this;
    }

    /**
     * Sets the mock file's name
     *
     * @param string $mockFileName
     * @return  MockMakerFileData
     */
    public function setMockFileName($mockFileName)
    {
        $this->mockFileName = $mockFileName;

        return $this;
    }

     /**
     * Sets the mock file's namespace
     *
     * @param   string  $mockFileNamespace
     * @return  MockMakerFileData
     */
    public function setMockFileNamespace($mockFileNamespace)
    {
        $this->mockFileNamespace = $mockFileNamespace;

        return $this;
    }

    /**
     * Sets the path to the project root directory
     *
     * @param   $projectRootPath    string
     * @return  MockMakerFileData
     */
    public function setProjectRootPath($projectRootPath)
    {
        $this->projectRootPath = $projectRootPath;

        return $this;
    }

    /**
     * Sets the directory to write mock files in
     *
     * @param   string $mockWriteDirectory
     * @return  MockMakerFileData
     */
    public function setMockWriteDirectory($mockWriteDirectory)
    {
        $this->mockWriteDirectory = $mockWriteDirectory;

        return $this;
    }

    /**
     * Sets the full path for saving the mock file
     *
     * @param   string
     * @return  MockMakerFileData
     */
    public function setMockFileSavePath($mockFileSavePath)
    {
        $this->mockFileSavePath = $mockFileSavePath;

        return $this;
    }

    /**
     * Sets the user defined mock file base namespace
     *
     * @param   string  $mockFileBaseNamespace
     * @return  MockMakerFileData
     */
    public function setMockFileBaseNamespace($mockFileBaseNamespace)
    {
        $this->mockFileBaseNamespace = $mockFileBaseNamespace;

        return $this;
    }

    /**
     * Sets whether or not to overwrite existing mock files
     *
     * @param   bool
     * @return  MockMakerFileData
     */
    public function setOverwriteExistingFiles($overwriteExistingFiles)
    {
        $this->overwriteExistingFiles = $overwriteExistingFiles;

        return $this;
    }

    /**
     * Sets the file's ClassData object
     *
     * @param   $classData  ClassData
     * @return  MockMakerFileData
     */
    public function setClassData(ClassData $classData)
    {
        $this->classData = $classData;

        return $this;
    }
}

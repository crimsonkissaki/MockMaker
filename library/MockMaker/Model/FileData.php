<?php

/**
 * FileData
 *
 * Holds all file information we need to create the mock file and
 * will be passed to the code generator classes later on
 *
 * @package     MockMaker
 * @author		Evan Johnson
 * @created     Apr 28, 2015
 * @version     1.0
 */

namespace MockMaker\Model;

use MockMaker\Model\ClassData;

class FileData
{

    /**
     * Fully qualified file path & name
     *
     * @var string
     */
    private $fullFilePath;

    /**
     * Simple file name, e.g. "Customer.php"
     *
     * @var type
     */
    private $fileName;

    /**
     * Path to the project root directory
     *
     * @var string
     */
    private $projectRootPath;

    /**
     * ClassData data object
     *
     * @var type
     */
    private $classData;

    /**
     * Gets the file's full path
     *
     * @return string
     */
    public function getFullFilePath()
    {
        return $this->fullFilePath;
    }

    /**
     * Gets the file's name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
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
     * Gets the file's ClassData object
     *
     * @return string
     */
    public function getClassData()
    {
        return $this->classData;
    }

    /**
     * Sets the file's full path
     *
     * @param   $fullFilePath   string
     * @return  FileData
     */
    public function setFullFilePath($fullFilePath)
    {
        $this->fullFilePath = $fullFilePath;

        return $this;
    }

    /**
     * Sets the file's name
     *
     * @param   $fileName   string
     * @return  FileData
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Sets the path to the project root directory
     *
     * @param   $projectRootPath    string
     */
    public function setProjectRootPath($projectRootPath)
    {
        $this->projectRootPath = $projectRootPath;
    }

    /**
     * Sets the file's ClassData object
     *
     * @param   $classData  ClassData
     * @return  FileData
     */
    public function setClassData(ClassData $classData)
    {
        $this->classData = $classData;

        return $this;
    }

}

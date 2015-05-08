<?php
/**
 * FileData
 *
 * This object holds file-level information for entity/mock classes.
 * File name, save location, etc.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/7/15
 * @version        1.0
 */

namespace MockMaker\Model;

class FileData
{

    /**
     * The file name for the class
     *
     * @var string
     */
    private $fileName;

    /**
     * Directory file is in
     *
     * @var string
     */
    private $fileDirectory;

    /**
     * Original source file
     *
     * @var string
     */
    private $fullFilePath;

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
     * Gets the file directory
     *
     * @return string
     */
    public function getFileDirectory()
    {
        return $this->fileDirectory;
    }

    /**
     * Gets the original location of a file
     *
     * @return string
     */
    public function getFullFilePath()
    {
        return $this->fullFilePath;
    }

    /**
     * Sets the file's name
     *
     * @param   string $fileName
     * @return  FileData
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Sets the file directory
     *
     * @param   string $fileDirectory
     * @return  FileData
     */
    public function setFileDirectory($fileDirectory)
    {
        $this->fileDirectory = $fileDirectory;

        return $this;
    }

    /**
     * Sets the original location of a file
     *
     * @param   string $fullFilePath
     * @return  FileData
     */
    public function setFullFilePath($fullFilePath)
    {
        $this->fullFilePath = $fullFilePath;

        return $this;
    }
}

<?php

/**
 * FileProcessorWorker
 *
 * Processes each file found by MockMaker
 *
 * @package     MockMaker
 * @author		Evan Johnson
 * @created     Apr 28, 2015
 * @version     1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;
use MockMaker\Model\FileData;
use MockMaker\Worker\FileDataWorker;
use MockMaker\Worker\ClassDataWorker;
use MockMaker\Helper\TestHelper;

class FileProcessorWorker
{

    /**
     * MockMaker config class
     *
     * @var ConfigData
     */
    private $config;

    /**
     * Class that handles processing for the FileData models
     *
     * @var FileDataWorker
     */
    private $fileDataWorker;

    /**
     * Array of FileData classes
     *
     * @var array
     */
    private $fileData = [ ];

    /**
     * Get the config file
     *
     * @return type
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Gets the FileDataWorker object
     *
     * @return  FileDataWorker
     */
    public function getFileDataWorker()
    {
        return $this->fileDataWorker;
    }

    /**
     * Gets the FileData objects
     *
     * @return  array
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    /**
     * Sets the array of FileData objects
     *
     * @param   object|array   $fileData    FileData objects
     * @return  void
     */
    public function setFileData($fileData)
    {
        $objs = is_array($fileData) ? $fileData : array( $fileData );
        $this->fileData = $objs;
    }

    /**
     * Adds (single|array of) FileData objects to fileData
     *
     * @param   object|array    $fileData   FileData objects
     * @return  void
     */
    public function addFileData($fileData)
    {
        if (is_array($fileData)) {
            $this->setFileData(array_merge($this->fileData, $fileData));
        } else {
            array_push($this->fileData, $fileData);
        }
    }

    /**
     * Sets the config file
     *
     * @param   ConfigData  $config     ConfigData object
     * @return  void
     */
    public function setConfig(ConfigData $config)
    {
        $this->config = $config;
    }

    /**
     * Instantiates a new FileProcessorWorker object
     *
     * @return  FileDataWorker
     */
    public function __construct()
    {
        $this->fileDataWorker = new FileDataWorker();
    }

    /**
     * Initiates file processing
     *
     * @return  string
     */
    public function processFiles()
    {
        $generatedCode = [ ];
        foreach ($this->config->getFilesToMock() as $file) {
            try {
                if ($fileData = $this->processFile($file, $this->config)) {
                    $key = $fileData->getClassData()->getClassName();
                    $generatedCode[$key] = '';
                }
            } catch (\Exception $e) {
                TestHelper::dbug($e->getMessage(), "Fatal MockMaker Exception:");
                continue;
            }
        }

        return $this;
        //return $generatedCode;
    }

    /**
     * Processes a single file for mocking
     *
     * @param   string      $file       File name to mock
     * @param   ConfigData  $config     ConfigData object
     * @return  FileData
     */
    private function processFile($file, ConfigData $config)
    {
        $fileData = $this->generateFileDataObject($file, $config);
        if (!in_array($fileData->getClassData()->getClassType(), array( 'abstract', 'interface' ))) {
            $this->addFileData($fileData);
            return $fileData;
        }

        return false;
    }

    /**
     * Creates a new FileData object
     *
     * @param   string      $file       File name to mock
     * @param   ConfigData  $config     ConfigData object
     * @return  FileData
     */
    private function generateFileDataObject($file, $config)
    {
        $mmFileObj = $this->fileDataWorker->generateNewObject($file, $config);
        // we need a new ClassWorker for each file
        $classWorker = new ClassDataWorker();
        $mmFileObj->setClassData($classWorker->generateNewObject($mmFileObj));

        return $mmFileObj;
    }

}

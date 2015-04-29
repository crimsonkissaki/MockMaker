<?php

/**
 * 	FileProcessorWorker
 *
 *  Processes each file found by MockMaker.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
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
     * Class that handles processing for the FileData models.
     *
     * @var FileDataWorker
     */
    private $mockMakerFileWorker;

    /**
     * Array of FileData classes.
     *
     * @var array
     */
    private $fileData = [ ];

    /**
     * Get the config file.
     *
     * @return type
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the FileDataWorker class instance.
     *
     * @return  FileDataWorker
     */
    public function getFileDataWorker()
    {
        return $this->mockMakerFileWorker;
    }

    /**
     * Get the mock maker files.
     *
     * @return  array
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    /**
     * Set the mock maker files array.
     *
     * @param   $fileData     array
     */
    public function setFileData($fileData)
    {
        $this->fileData = $fileData;
    }

    /**
     * Add single or array of FileData objects to fileData.
     *
     * @param   $fileData     mixed
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
     * Set the config file.
     *
     * @param   $config   ConfigData
     */
    public function setConfig(ConfigData $config)
    {
        $this->config = $config;
    }

    /**
     * Instantiate a new FileProcessorWorker object.
     */
    public function __construct()
    {
        $this->mockMakerFileWorker = new FileDataWorker();
    }

    /**
     * Kick off file processing.
     *
     * @return  string
     */
    public function processFiles()
    {
        foreach ($this->config->getFilesToMock() as $file) {
            try {
                $this->processFile($file, $this->config);
            } catch (\Exception $e) {
                //$msg = $e->getTraceAsString() . "\n\n" . $e->getMessage();
                //TestHelper::dbug($msg, "Fatal MockMaker Exception:");
                TestHelper::dbug($e->getMessage(), "Fatal MockMaker Exception:");
                continue;
            }
        }

        return $this;
    }

    /**
     * Process a single file for mocking.
     *
     * @param   $file       string
     * @param   $config     ConfigData
     */
    private function processFile($file, ConfigData $config)
    {
        $mockMakerFile = $this->generateFileDataObject($file, $config);
        if (!in_array($mockMakerFile->getClassData()->getClassType(), array( 'abstract', 'interface' ))) {
            $this->addFileData($mockMakerFile);
        }
    }

    /**
     * Create a new FileData object.
     *
     * @param   $file           string
     * @param   $config         ConfigData
     * @return  FileData
     */
    private function generateFileDataObject($file, $config)
    {
        $mmFileObj = $this->mockMakerFileWorker->generateNewObject($file, $config);
        // we need a new ClassWorker for each file
        $classWorker = new ClassDataWorker();
        $mmFileObj->setClassData($classWorker->generateNewObject($mmFileObj));

        return $mmFileObj;
    }

}

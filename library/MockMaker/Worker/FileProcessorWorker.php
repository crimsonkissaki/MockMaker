<?php

/**
 * FileProcessorWorker
 *
 * Processes each file found by MockMaker
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;
use MockMaker\Model\MockMakerFileData;
use MockMaker\Worker\MockMakerFileDataWorker;
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
     * Class that handles processing for the MockMakerFileData models
     *
     * @var MockMakerFileDataWorker
     */
    private $fileDataWorker;

    /**
     * Array of MockMakerFileData classes
     *
     * @var array
     */
    private $fileData = [];

    /**
     * Get the config file
     *
     * @return ConfigData
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Gets the MockMakerFileDataWorker object
     *
     * @return  MockMakerFileDataWorker
     */
    public function getFileDataWorker()
    {
        return $this->fileDataWorker;
    }

    /**
     * Gets the MockMakerFileData objects
     *
     * @return  array
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    /**
     * Sets the array of MockMakerFileData objects
     *
     * @param   array $fileData MockMakerFileData objects
     * @return  void
     */
    public function setFileData(array $fileData)
    {
        $this->fileData = $fileData;
    }

    /**
     * Adds (single|array of) MockMakerFileData objects to fileData
     *
     * @param   object|array $fileData MockMakerFileData objects
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
     * Instantiates a new FileProcessorWorker object
     *
     * @param ConfigData $configData
     */
    public function __construct(ConfigData $configData)
    {
        $this->config = $configData;
        $this->fileDataWorker = new MockMakerFileDataWorker();
    }

    /**
     * Process the files into usable objects
     *
     * Returns array of MockMakerFileData objects for use in the CodeWorker.
     *
     * @return  array
     */
    public function processFiles()
    {
        foreach ($this->config->getFilesToMock() as $file) {
            try {
                $this->processFile($file, $this->config);
            } catch (\Exception $e) {
                TestHelper::dbug($e->getMessage(), "Fatal MockMaker Exception:");
                continue;
            }
        }

        return $this->getFileData();
    }

    /**
     * Processes a single file for mocking
     *
     * @param   string     $file   File name to mock
     * @param   ConfigData $config ConfigData object
     * @return  MockMakerFileData
     */
    private function processFile($file, ConfigData $config)
    {
        $fileData = $this->generateFileDataObject($file, $config);
        if (in_array($fileData->getClassData()->getClassType(), array('abstract', 'interface'))) {
            return false;
        }
        $this->addFileData($fileData);

        return $fileData;
    }

    /**
     * Creates a new MockMakerFileData object
     *
     * @param   string     $file   File name to mock
     * @param   ConfigData $config ConfigData object
     * @return  MockMakerFileData
     */
    private function generateFileDataObject($file, $config)
    {
        $fileData = $this->fileDataWorker->generateNewObject($file, $config);
        // we need a new ClassWorker for each file
        $classWorker = new ClassDataWorker();
        $fileData->setClassData($classWorker->generateNewObject($fileData));

        return $fileData;
    }
}

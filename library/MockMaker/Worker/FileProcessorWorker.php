<?php

/**
 * FileProcessorWorker
 *
 * Primary processor class for queued target files.
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Worker;

use MockMaker\Exception\MockMakerException;
use MockMaker\Model\ConfigData;
use MockMaker\Model\DataContainer;

class FileProcessorWorker
{

    /**
     * MockMaker config class
     *
     * @var ConfigData
     */
    private $config;

    /**
     * Class that handles processing for the DataContainer models
     *
     * @var DataContainerWorker
     */
    private $dataContainerWorker;

    /**
     * Array of DataPointWorkers that need to be executed
     *
     * @var array
     */
    private $dataPointWorkers = [];

    /**
     * Array of DataContainer classes
     *
     * @var array
     */
    private $fileData = [];

    /**
     * Array of mock code
     *
     * @var array
     */
    private $mockCode = [];

    /**
     * Sets the array of DataContainer objects
     *
     * @param   array $fileData DataContainer objects
     * @return  void
     */
    private function setFileData(array $fileData)
    {
        $this->fileData = $fileData;
    }

    /**
     * Adds (single|array of) DataContainer objects to fileData
     *
     * @param   object|array $fileData DataContainer objects
     * @return  void
     */
    private function addFileData($fileData)
    {
        if (is_array($fileData)) {
            $this->setFileData(array_merge($this->fileData, $fileData));
        } else {
            array_push($this->fileData, $fileData);
        }
    }

    /**
     * Adds (single|array of) mock code strings to mockCode
     *
     * @param   string|array $mockCode Mock code generated from DataContainer object
     * @return  void
     */
    private function addMockCode($mockCode)
    {
        if (is_array($mockCode)) {
            $this->setFileData(array_merge($this->mockCode, $mockCode));
        } else {
            array_push($this->mockCode, $mockCode);
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
        $this->dataContainerWorker = new DataContainerWorker();
        $this->dataPointWorkers = $configData->getDataPointWorkers();
    }

    /**
     * Process the file queue into usable objects
     *
     * Returns an array of DataContainer objects for use in the CodeWorker.
     *
     * @return  array
     */
    public function processFiles()
    {
        foreach ($this->config->getFilesToMock() as $file) {
            try {
                $fileData = $this->processFile($file, $this->config);
                $this->processDataWithWorkers($fileData);
            } catch (MockMakerException $e) {
                echo "\nMockMakerException: {$e->getMessage()}\n";
                continue;
            }
        }

        return implode(PHP_EOL . str_repeat("-", 50) . PHP_EOL, $this->mockCode);
    }

    /**
     * Processes a single target file for mocking
     *
     * @param   string     $file   File name to mock
     * @param   ConfigData $config ConfigData object
     * @return  DataContainer
     */
    private function processFile($file, ConfigData $config)
    {
        $fileData = $this->dataContainerWorker->generateDataContainerObject($file, $config);
        $this->addFileData($fileData);

        return $fileData;
    }

    /**
     * Generates required code from a DataContainer object
     *
     * @param   DataContainer $dataContainer
     * @return  string
     */
    private function processDataWithWorkers(DataContainer $dataContainer)
    {
        $templateWorker = new TemplateWorker();
        foreach ($this->dataPointWorkers as $worker) {
            $mockCode = $templateWorker->processWithWorker($worker, $dataContainer);
            $this->addMockCode($mockCode);
        }

        return $mockCode;
    }
}

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

use MockMaker\Model\MockMakerConfig;
use MockMaker\Model\MockMakerFile;
use MockMaker\Worker\MockMakerFileWorker;

class FileProcessorWorker
{

    /**
     * MockMaker config class
     *
     * @var MockMakerConfig
     */
    private $config;

    /**
     * Class that handles processing for the MockMakerFile models.
     *
     * @var MockMakerFileWorker
     */
    private $mockMakerFileWorker;

    /**
     * Array of MockMakerFile classes.
     *
     * @var array
     */
    private $mockMakerFiles = [ ];

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
     * Get the MockMakerFileWorker class instance.
     *
     * @return  MockMakerFileWorker
     */
    public function getMockMakerFileWorker()
    {
        return $this->mockMakerFileWorker;
    }

    /**
     * Get the mock maker files.
     *
     * @return  array
     */
    public function getMockMakerFiles()
    {
        return $this->mockMakerFiles;
    }

    /**
     * Set the mock maker files array.
     *
     * @param   $mockMakerFiles     array
     */
    public function setMockMakerFiles($mockMakerFiles)
    {
        $this->mockMakerFiles = $mockMakerFiles;
    }

    /**
     * Add single or array of MockMakerFile objects to mockMakerFiles.
     *
     * @param   $mockMakerFiles     mixed
     */
    public function addMockMakerFiles($mockMakerFiles)
    {
        if (is_array($mockMakerFiles)) {
            $this->setMockMakerFiles(array_merge($this->mockMakerFiles,
                    $mockMakerFiles));
        } else {
            array_push($this->mockMakerFiles, $mockMakerFiles);
        }
    }

    /**
     * Set the config file.
     *
     * @param   $config   MockMakerConfig
     */
    public function setConfig(MockMakerConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Instantiate a new FileProcessorWorker object.
     */
    public function __construct()
    {
        $this->mockMakerFileWorker = new MockMakerFileWorker();
    }

    /**
     * Kick off file processing.
     *
     * @return  string
     */
    public function processFiles()
    {
        foreach ($this->config->getFilesToMock() as $file) {
            $this->processFile($file);
        }

        return $this;
    }

    /**
     * Process a single file for mocking.
     *
     * @param   $file   string
     */
    private function processFile($file)
    {
        /**
         * ok, what do we need to do here?
         * pull in the file we want to process
         * assign it to a FileModel...
         * get the "file properties"
         * - get properties for each method
         * - get property properties
         * - can then pass off that model to the code processor
         */
        $this->addMockMakerFiles($this->createMockMakerFileObject($file));
    }

    /**
     * Create a new MockMakerFile object.
     *
     * @param   $file           string
     * @return  MockMakerFile
     */
    private function createMockMakerFileObject($file)
    {
        $mmFileObj = $this->mockMakerFileWorker->generateNewObject($file);

        return $mmFileObj;
    }

}
<?php
/**
 * ConfigDataWorker
 *
 * Handles some data processing for the ConfigData class
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/6/15
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;

class ConfigDataWorker
{

    /**
     * Generates data that might be missing from the ConfigData class so processing can proceed
     *
     * @param   ConfigData $config ConfigData class with only user-supplied data
     * @return  ConfigData
     */
    public function validateRequiredConfigData(ConfigData $config)
    {
        $config->setProjectRootPath($this->validateRootPath($config));
        $config->addToAllDetectedFiles($this->findAllTargetFiles($config));
        $config->addFilesToMock($this->filterUnwantedFiles($config));

        return $config;
    }

    /**
     * Returns a valid root path
     *
     * @param   ConfigData $config
     * @return  string
     */
    private function validateRootPath(ConfigData $config)
    {
        return ($config->getProjectRootPath()) ?: DirectoryWorker::guessProjectRootPath();
    }

    /**
     * Gathers all files found in read directories
     *
     * @param   ConfigData $config
     * @return  array
     */
    private function findAllTargetFiles(ConfigData $config)
    {
        return DirectoryWorker::getFilesFromReadDirs($config->getReadDirectories(), $config->getRecursiveRead());
    }

    /**
     * FIlters files based on regex options
     *
     * @param   ConfigData $config
     * @return  array
     */
    private function filterUnwantedFiles(ConfigData $config)
    {
        $fileWorker = new FileWorker();

        return $fileWorker->filterFilesWithRegex($config);
    }
}
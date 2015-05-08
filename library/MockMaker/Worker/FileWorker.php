<?php

/**
 * FileWorker
 *
 * File operations for MockMaker
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 26, 2015
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;
use MockMaker\Exception\MockMakerException;
use MockMaker\Exception\MockMakerErrors;

class FileWorker
{

    /**
     * Validates all manually provided files
     *
     * @param   array $files Files to mock
     * @return  bool
     * @throws  MockMakerException
     */
    public static function validateFiles(array $files)
    {
        foreach ($files as $file) {
            if (!is_readable($file)) {
                throw new MockMakerException(
                    MockMakerErrors::generateMessage(MockMakerErrors::INVALID_SOURCE_FILE, array('file' => $file))
                );
            }
        }

        return true;
    }

    /**
     * Returns the results of the provided regex patterns on target files
     *
     * @param   ConfigData $config ConfigData object
     * @return  array
     */
    public function testRegexPatterns(ConfigData $config)
    {
        $files = $config->getAllDetectedFiles();

        return array(
            'include'  => $this->getIncludeFiles($files, $config->getIncludeFileRegex()),
            'exclude'  => $this->getExcludeFiles($files, $config->getExcludeFileRegex()),
            'workable' => $this->filterFilesWithRegex($config),
        );
    }

    /**
     * Removes any files that don't pass regex validation
     *
     * @param   ConfigData $config ConfigData object
     * @return  array
     */
    public function filterFilesWithRegex(ConfigData $config)
    {
        $include = $this->getIncludeFiles($config->getAllDetectedFiles(), $config->getIncludeFileRegex());
        $exclude = $this->getExcludeFiles($config->getAllDetectedFiles(), $config->getExcludeFileRegex());
        $validFiles = array_values(array_diff($include, $exclude));

        return $validFiles;
    }

    /**
     * Gets files that are to be included in the mocking set
     *
     * @param   array  $files Manually specified or read directory files
     * @param   string $regex Include regex string
     * @return  array
     */
    private function getIncludeFiles($files, $regex)
    {
        $include = $files;
        if (!empty($regex)) {
            $include = $this->getFilesThatMatchRegex($files, $regex);
        }

        return $include;
    }

    /**
     * Gets files that are to be excluded in the mocking set
     *
     * @param   array  $files Manually specified or read directory files
     * @param   string $regex Exclude regex string
     * @return  array
     */
    private function getExcludeFiles($files, $regex)
    {
        $exclude = [];
        if (!empty($regex)) {
            $exclude = $this->getFilesThatMatchRegex($files, $regex);
        }

        return $exclude;
    }

    /**
     * Returns files from an array that match a regex pattern
     *
     * @param   array  $files Files to filter
     * @param   string $regex Regex to use on file names
     * @return  array
     * @throws  MockMakerException
     */
    private function getFilesThatMatchRegex($files, $regex)
    {
        $matches = [];
        try {
            if (!empty($regex)) {
                $matches = $this->getMatchingFiles($files, $regex);
            }
        } catch (\Exception $e) {
            throw new MockMakerException(
                MockMakerErrors::generateMessage(MockMakerErrors::INVALID_REGEX, array('regex' => $regex))
            );
        }

        return $matches;
    }

    /**
     * Returns php files that match a regex pattern
     *
     * @param   array  $files Files to filter
     * @param   string $regex Regex to use on file names
     * @return  array
     */
    private function getMatchingFiles($files, $regex)
    {
        $matches = [];
        foreach ($files as $file) {
            if (substr($file, -4) !== '.php') {
                continue;
            }
            $fileName = str_replace('.php', '', join('', array_slice(explode('/', $file), -1)));
            if (preg_match($regex, $fileName) === 1) {
                $matches[] = $file;
            }
        }

        return $matches;
    }
}

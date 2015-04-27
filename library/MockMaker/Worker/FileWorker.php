<?php

/**
 * 	FileWorker
 *
 *  File operations for MockMaker
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 26, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerConfig as Config;
use MockMaker\Exception\MockMakerException as MMException;
use MockMaker\Exception\MockMakerErrors as MMErrors;

class FileWorker
{

    /**
     * Validate all provided files.
     *
     * @param   $config     Config      MockMaker configuration object
     * @return  bool
     * @throws  MockMakerException
     */
    public function validateFiles(Config $config)
    {
        foreach ($config->getFilesToMock() as $file) {
            if (!is_readable($file)) {
                throw new MMException(MMErrors::generateMessage(MMErrors::INVALID_SOURCE_FILE, array( 'file' => "'{$file}'" )));
            }
        }

        return true;
    }

    public function testRegexPatterns(Config $config)
    {
        $includeFiles = $this->testIncludeRegex($config->getIncludeFileRegex(), $config->getFilesToMock());
        $excludeFiles = $this->testExcludeRegex($config->getExcludeFileRegex(), $config->getFilesToMock());

        return [ ];
    }

    private function testIncludeRegex()
    {

    }

    private function testExcludeRegex()
    {

    }

}

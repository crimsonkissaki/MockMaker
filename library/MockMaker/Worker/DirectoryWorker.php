<?php

/**
 * 	DirectoryWorker
 *
 *  Directory operations for MockMaker
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 26, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerConfig as Config;
use MockMaker\Exception\MockMakerException as MMException;
use MockMaker\Exception\MockMakerErrors as MMErrors;

class DirectoryWorker
{

    /**
     * Validate all directories are valid and have correct permissions.
     *
     * @param   $config     Config      MockMaker configuration object
     * @return  bool
     */
    public function validateDirectories(Config $config)
    {
        $this->validateReadDirs($config->getReadDirectories());
        $this->validateWriteDir($config->getWriteDirectory());

        return true;
    }

    /**
     * Validate specified read directories.
     *
     * @param   $dirs   array
     * @return  bool
     * @throws  MockMakerException
     */
    private function validateReadDirs($dirs)
    {
        foreach ($dirs as $k => $dir) {
            if (!is_dir($dir)) {
                throw new MMException(MMErrors::generateMessage(MMErrors::READ_DIR_NOT_EXIST, array( 'dir' => "'{$dir}'" )));
            }
            if (!is_readable($dir)) {
                throw new MMException(MMErrors::generateMessage(MMErrors::READ_DIR_INVALID_PERMISSIONS, array( 'dir' => "'{$dir}'" )));
            }
        }

        return true;
    }

    /**
     * Validate the specified write directory.
     *
     * This will attempt to create the write directory
     * if it does not already exist.
     *
     * @param   $dir    string
     * @return  bool
     * @throws  MockMakerException
     */
    private function validateWriteDir($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777)) {
                throw new MMException(MMErrors::generateMessage(MMErrors::WRITE_DIR_NOT_EXIST, array( 'dir' => "'{$dir}'" )));
            }
        }
        if (!is_writeable($dir)) {
            throw new MMException(MMErrors::generateMessage(MMErrors::WRITE_DIR_INVALID_PERMISSIONS, array( 'dir' => "'{$dir}'" )));
        }

        return true;
    }

}

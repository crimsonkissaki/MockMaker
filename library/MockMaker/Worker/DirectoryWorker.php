<?php

/**
 * 	DirectoryWorker
 *
 *  Directory operations for MockMaker
 *
 * 	@author		Evan Johnson
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
     * Validate specified read directories.
     *
     * @param   $dirs   array
     * @return  bool
     * @throws  MockMakerException
     */
    public function validateReadDirs($dirs)
    {
        foreach ($dirs as $k => $dir) {
            if (!is_dir($dir)) {
                throw new MMException(MMErrors::generateMessage(MMErrors::READ_DIR_NOT_EXIST,
                    array( 'dir' => "'{$dir}'" )));
            }
            if (!is_readable($dir)) {
                throw new MMException(MMErrors::generateMessage(MMErrors::READ_DIR_INVALID_PERMISSIONS,
                    array( 'dir' => "'{$dir}'" )));
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
    public function validateWriteDir($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777)) {
                throw new MMException(MMErrors::generateMessage(MMErrors::WRITE_DIR_NOT_EXIST,
                    array( 'dir' => "'{$dir}'" )));
            }
        }
        if (!is_writeable($dir)) {
            throw new MMException(MMErrors::generateMessage(MMErrors::WRITE_DIR_INVALID_PERMISSIONS,
                array( 'dir' => "'{$dir}'" )));
        }

        return true;
    }

    /**
     * Try to guess the project root path.
     *
     * First attempt to find the 'vendor' directory in the file path,
     * and return the path up to that point.
     * If there is no 'vendor' directory, then we try to find it based
     * on the directory structure of this file.
     *
     * @return  string
     */
    public function guessProjectRootPath()
    {
        $vendorPos = strpos(__FILE__, 'vendor');
        if ($vendorPos !== false) {
            return substr(__FILE__, 0, $vendorPos);
        }

        return dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    }

}

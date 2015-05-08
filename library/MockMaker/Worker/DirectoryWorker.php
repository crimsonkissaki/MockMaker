<?php

/**
 * DirectoryWorker
 *
 * Performs directory related operations for MockMaker
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 26, 2015
 * @version       1.0
 */

namespace MockMaker\Worker;

use MockMaker\Exception\MockMakerException;
use MockMaker\Exception\MockMakerErrors;

class DirectoryWorker
{

    /**
     * Validates specified read directories
     *
     * @param   array $dirs Specified read directories
     * @return  bool
     * @throws  MockMakerException
     */
    public static function validateReadDirs($dirs)
    {
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                throw new MockMakerException(
                    MockMakerErrors::generateMessage(MockMakerErrors::READ_DIR_NOT_EXIST, array('dir' => "'{$dir}'"))
                );
            }
            if (!is_readable($dir)) {
                throw new MockMakerException(
                    MockMakerErrors::generateMessage(MockMakerErrors::READ_DIR_INVALID_PERMISSIONS,
                        array('dir' => "'{$dir}'"))
                );
            }
        }

        return true;
    }

    /**
     * Verifies that a directory is valid
     *
     * @param   string $dir      Directory to validate
     * @param   string $errorMsg Error to display if invalid
     * @throws  MockMakerException
     */
    public static function checkIsValidDirectory($dir, $errorMsg = MockMakerErrors::INVALID_DIR)
    {
        if (!is_dir($dir)) {
            throw new MockMakerException(
                MockMakerErrors::generateMessage($errorMsg, array('dir' => $dir))
            );
        }
    }

    /**
     * Validates the specified write directory
     *
     * This will attempt to create the write directory
     * if it does not already exist.
     *
     * @param   string $dir Write directory
     * @return  bool
     * @throws  MockMakerException
     */
    public static function validateWriteDir($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new MockMakerException(
                    MockMakerErrors::generateMessage(MockMakerErrors::WRITE_DIR_CANNOT_CREATE, array('dir' => $dir))
                );
            }
        }
        if (!is_writeable($dir)) {
            throw new MockMakerException(
                MockMakerErrors::generateMessage(MockMakerErrors::WRITE_DIR_INVALID_PERMISSIONS,
                    array('dir' => "'{$dir}'"))
            );
        }

        return true;
    }

    /**
     * Tries to guess the project root path
     *
     * First attempts to find the 'vendor' directory in the file path,
     * and return the path up to that point. If there is no 'vendor'
     * directory then try based on the directory structure of this file.
     *
     * @return  string
     */
    public static function guessProjectRootPath()
    {
        $vendorPos = strpos(__FILE__, 'vendor');
        if ($vendorPos !== false) {
            return substr(__FILE__, 0, $vendorPos);
        }

        return dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    }

    /**
     * Gets all files found in any specified read directories
     *
     * @param   array $allReadDirs array   Read directories
     * @param   bool  $recurse     bool    Recursively scan directories or not
     * @return  array
     */
    public static function getFilesFromReadDirs($allReadDirs, $recurse = false)
    {
        if (empty($allReadDirs)) {
            return [];
        }
        $dirs = [];
        $files = [];
        foreach ($allReadDirs as $dir) {
            $dirs[] = (!$recurse) ? new \DirectoryIterator($dir) : new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        }
        foreach ($dirs as $dir) {
            foreach ($dir as $file) {
                if (!$file->isDir() && $file->getExtension() === 'php') {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }
}


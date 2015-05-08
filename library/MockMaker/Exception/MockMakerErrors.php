<?php

/**
 * MockMakerErrors
 *
 * Error messages for MockMaker
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 19, 2015
 * @version       1.0
 */

namespace MockMaker\Exception;

use MockMaker\Worker\StringFormatterWorker;

class MockMakerErrors
{

    // Generic
    const INVALID_DIR = "INVALID_DIR_MSG";
    const INVALID_DIR_MSG = "ERROR: Directory '{%dir%}' does not seem to be valid.";
    // Config
    const INVALID_PROJECT_ROOT_PATH = 'INVALID_PROJECT_ROOT_PATH_MSG';
    const INVALID_PROJECT_ROOT_PATH_MSG = "ERROR: Provided project root path '%dir%' appears invalid.";
    const INVALID_DATA_POINT_WORKER = 'INVALID_DATA_POINT_WORKER_MSG';
    const INVALID_DATA_POINT_WORKER_MSG = 'ERROR: \'%class%\' must be an instance of AbstractDataPointWorker.';
    // MockMaker
    const WRITE_DIR_NOT_EXIST = "WRITE_DIR_NOT_EXIST_MSG";
    const WRITE_DIR_NOT_EXIST_MSG = "Write directory (%dir%) does not exist.";
    const WRITE_DIR_INVALID_PERMISSIONS = "WRITE_DIR_INVALID_PERMISSIONS_MSG";
    const WRITE_DIR_INVALID_PERMISSIONS_MSG = "Write directory (%dir%) has insufficient permissions for writing.";
    const WRITE_DIR_CANNOT_CREATE = "WRITE_DIR_CANNOT_CREATE_MSG";
    const WRITE_DIR_CANNOT_CREATE_MSG = "Unable to create non-existent write directory (%dir%).";
    const READ_DIR_NOT_EXIST = "READ_DIR_NOT_EXIST_MSG";
    const READ_DIR_NOT_EXIST_MSG = "Read directory (%dir%) does not exist.";
    const READ_DIR_INVALID_PERMISSIONS = "READ_DIR_INVALID_PERMISSIONS_MSG";
    const READ_DIR_INVALID_PERMISSIONS_MSG = "Read directory (%dir%) has insufficient permissions for reading.";
    const CLASS_CANNOT_BE_MOCKED = "CLASS_CANNOT_BE_MOCKED_MSG";
    const CLASS_CANNOT_BE_MOCKED_MSG = "Unknown error while attempting to generate mock for class '%class%'.";
    // FileWorker
    const INVALID_REGEX = "INVALID_REGEX_MSG";
    const INVALID_REGEX_MSG = "Provided regex '%regex%' is invalid.";
    // PropertyWorker
    const PW_INVALID_CLASS_INSTANCE = "PW_INVALID_CLASS_INSTANCE_MSG";
    const PW_INVALID_CLASS_INSTANCE_MSG = "PropertyWorker requires a valid instance of class '%class%' to mock.";
    // EntityDataWorker
    const CLASS_HAS_CONSTRUCTOR = 'CLASS_HAS_CONSTRUCTOR_MSG';
    const CLASS_HAS_CONSTRUCTOR_MSG = "Class '%class%' has a constructor with mandatory arguments and cannot be mocked.";
    const INVALID_CLASS_TYPE = 'INVALID_CLASS_TYPE_MSG';
    const INVALID_CLASS_TYPE_MSG = "Class '%class%' is either abstract or an interface and cannot be mocked.";
    // File Generator
    const INVALID_SOURCE_FILE = "INVALID_SOURCE_FILE_MSG";
    const INVALID_SOURCE_FILE_MSG = "Unable to read file '%file%'.";
    // DataPointWorker
    const INVALID_TEMPLATE_FILE = 'INVALID_TEMPLATE_FILE_MSG';
    const INVALID_TEMPLATE_FILE_MSG = 'ERROR: Defined template \'%file%\' is not a valid file.';
    const TEMPLATE_FILE_NOT_DEFINED = 'TEMPLATE_FILE_NOT_DEFINED_MSG';
    const TEMPLATE_FILE_NOT_DEFINED_MSG = 'ERROR: No template file defined for DataPointWorker \'%worker%\'.';
    const DATA_POINT_WORKER_WRITE_ERR = 'DATA_POINT_WORKER_WRITE_ERR_MSG';
    const DATA_POINT_WORKER_WRITE_ERR_MSG = 'ERROR: Unexpected error writing to \'%path%\'';

    /**
     * Generates a formatted message string
     *  
     *
     * @param    string $code   Message code to use
     * @param    array  $params Optional params to insert into message text
     * @return   string
     * @throws   \InvalidArgumentException
     */
    public static function generateMessage($code, $params = [])
    {
        $class = __CLASS__;
        if (!defined("$class::$code")) {
            throw new \InvalidArgumentException("{$code} is not a valid error code.");
        }

        return StringFormatterWorker::vsprintf2(constant("$class::{$code}"), $params);
    }
}


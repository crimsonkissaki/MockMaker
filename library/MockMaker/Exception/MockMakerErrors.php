<?php

/**
 * 	MockMakerErrors
 *
 * 	Error messages for MockMaker
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 19, 2015
 * 	@version	1.0
 */

namespace MockMaker\Exception;

use MockMaker\Worker\StringFormatterWorker;

class MockMakerErrors
{

    // MockMaker
    const WRITE_DIR_NOT_EXIST = "WRITE_DIR_NOT_EXIST";
    const WRITE_DIR_NOT_EXIST_MSG = "Write directory (%dir%) does not exist.";
    const WRITE_DIR_INVALID_PERMISSIONS = "WRITE_DIR_INVALID_PERMISSIONS";
    const WRITE_DIR_INVALID_PERMISSIONS_MSG = "Write directory (%dir%) has insufficient permissions for writing.";
    const READ_DIR_NOT_EXIST = "READ_DIR_NOT_EXIST";
    const READ_DIR_NOT_EXIST_MSG = "Read directory (%dir%) does not exist.";
    const READ_DIR_INVALID_PERMISSIONS = "READ_DIR_INVALID_PERMISSIONS";
    const READ_DIR_INVALID_PERMISSIONS_MSG = "Read directory (%dir%) has insufficient permissions for reading.";
    const CLASS_CANNOT_BE_MOCKED = "CLASS_CANNOT_BE_MOCKED";
    const CLASS_CANNOT_BE_MOCKED_MSG = "An unknown problem ocurred while attempting to generate a mock file.";
    // PropertyWorker
    const PW_INVALID_CLASS_INSTANCE = "PW_INVALID_CLASS_INSTANCE";
    const PW_INVALID_CLASS_INSTANCE_MSG = "PropertyWorker requires a valid instance of class %class% to mock.";
    // File Generator
    const INVALID_SOURCE_FILE = "INVALID_SOURCE_FILE";
    const INVALID_SOURCE_FILE_MSG = "Unable to read file '%file%'.";

    /**
     * Generate a message code.
     * Allows insertion of custom values into the standardized notification messages.
     *  
     * @param	$locationId		string	Location id
     * @param	$code			string	Message code to use.
     * @param	$params			array	Optional params to insert into message text
     * @throws	InvalidArgumentException
     * @return	array
     */
    public static function generateMessage($code, $params = [ ])
    {
        $class = 'Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerErrors';
        if (is_null(constant("$class::$code"))) {
            throw new \InvalidArgumentException("{$code} is not a valid error code.");
        }

        return StringFormatterWorker::vsprintf2(constant("$class::{$code}_MSG"), $params);
    }

}

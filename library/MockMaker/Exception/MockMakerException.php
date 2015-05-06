<?php

/**
 * MockMakerException
 *
 * Exception class specific for MockMaker
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 18, 2015
 * @version       1.0
 */

namespace MockMaker\Exception;

class MockMakerException extends \Exception
{

    /**
     * Constructs a new MockMakerException
     *
     * @param    string     $message  Exception string
     * @param    int        $code     Code number
     * @param    \Exception $previous Previous \Exception
     * @return  MockMakerException
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

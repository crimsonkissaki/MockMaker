<?php
/**
 * MockMakerFatalException
 *
 * @package:    MockMaker
 * @author :     Evan Johnson
 * @created:    5/5/15
 */

namespace MockMaker\Exception;

class MockMakerFatalException extends \Exception
{

    /**
     * Constructs a new MockMakerFatalException
     *
     * This exception is used when the error requires MockMaker to completely stop.
     *
     * @param    string     $message  Exception string
     * @param    int        $code     Code number
     * @param    \Exception $previous Previous \Exception
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
<?php

/**
 *	MockMakerException
 *
 *	Exception class specific for MockMaker
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 18, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Exception;

use Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerErrors;

class MockMakerException extends \Exception
{

	/**
	 * Construct a new MockMakerException
	 *
	 * @param	$message	string
	 * @param	$code		int
	 * @param	$previous	\Exception
	 * @throws	\InvalidArgumentException
	 */
	public function __construct($message, $code = 0, \Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
    }

}

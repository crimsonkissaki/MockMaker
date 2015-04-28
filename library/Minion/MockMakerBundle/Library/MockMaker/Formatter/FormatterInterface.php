<?php

/**
 *	FormatterInterface
 *
 *	@author		Evan Johnson
 *	@created	Apr 16, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Formatter;

use Minion\MockMakerBundle\Library\MockMaker;

interface FormatterInterface
{

	public function generateMockCode( MockMaker $mockMaker );

}

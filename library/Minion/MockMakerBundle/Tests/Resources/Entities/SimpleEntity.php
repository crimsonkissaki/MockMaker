<?php

/**
 *	SimpleEntity
 *
 *	A very simple entity used for testing.
 *
 *	@author		Evan Johnson
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Resources\Entities;

class SimpleEntity
{

	public $publicProperty;

	private $privateProperty;

	protected $protectedProperty;

	public function publicFunction()
	{
		return true;
	}

	private function privateFunction()
	{
		return true;
	}

	protected function protectedFunction()
	{
		return true;
	}

}

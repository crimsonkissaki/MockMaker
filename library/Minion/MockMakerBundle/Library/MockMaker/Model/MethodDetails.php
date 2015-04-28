<?php

/**
 *	MethodDetails
 *
 *	@author		Evan Johnson
 *	@created	Apr 18, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Model;

class MethodDetails
{

	/**
	 * Name of the property
	 *
	 * @var	string
	 */
	public $name;

	/**
	 * Method visibility scope
	 *
	 * @var string
	 */
	public $scope;

	/**
	 * Is method a setter?
	 *
	 * @var bool
	 */
	public $isSetter;

	/**
	 * Method arguments
	 *
	 * @var array
	 */
	public $arguments = [];

}

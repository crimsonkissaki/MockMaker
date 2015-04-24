<?php

/**
 *	ArgumentDetails
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 18, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Model;

class ArgumentDetails
{

	/**
	 * Name of the argument
	 *
	 * @var	string
	 */
	public $name;

	/**
	 * Argument type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Typehinted value, if any.
	 *
	 * @var	string
	 */
	public $typeHint;

	/**
	 * Argument's default value
	 *
	 * @var	mixed
	 */
	public $defaultValue;

	/**
	 * Is argument required?
	 *
	 * @var bool
	 */
	public $isRequired = TRUE;

	/**
	 * Can argument be null?
	 *
	 * @var	bool
	 */
	public $allowsNull;

	/**
	 * Is argument passed by reference?
	 *
	 * @var	bool
	 */
	public $passedByReference = FALSE;

}

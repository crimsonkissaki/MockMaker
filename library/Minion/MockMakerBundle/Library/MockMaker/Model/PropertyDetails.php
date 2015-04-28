<?php

/**
 *	PropertyDetails
 *
 *	@author		Evan Johnson
 *	@created	Apr 18, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Model;

class PropertyDetails
{

	/**
	 * Name of the property
	 *
	 * @var	string
	 */
	public $name;

	/**
	 * Visibility scope
	 *
	 * @var string
	 */
	public $scope;

	/**
	 * Default property value (if any):
	 *   - assigned statically
	 *   - set in the constructor
	 *
	 * @var string
	 */
	public $defaultValue;

	/**
	 * Is the property static?
	 *
	 * @var bool
	 */
	public $isStatic;

	/**
	 * Property setter method.
	 *
	 * @var string
	 */
	public $setter;

	/**
	 * Property type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Property class name/typehint (if type === object).
	 *
	 * @var string
	 */
	public $typeHint;

}

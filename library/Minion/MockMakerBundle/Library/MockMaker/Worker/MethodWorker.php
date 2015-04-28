<?php

/**
 *	MethodWorker
 *
 *	@author		Evan Johnson
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Worker;

use Minion\MockMakerBundle\Library\MockMaker\Model\MethodDetails;
use Minion\MockMakerBundle\Library\MockMaker\Worker\ArgumentWorker;
use Minion\MockMakerBundle\Library\MockMaker\Model\ArgumentDetails;

use Minion\UnitTestBundle\Library\DebuggerMinion;

class MethodWorker
{

	private $argumentWorker;

	public function __construct()
	{
		$this->argumentWorker = new ArgumentWorker;
	}

	/**
	 * Get all of the class's method's details.
	 *
	 * @param	$classMethods	array	Array of \ReflectionMethod objects.
	 * @return	array
	 */
	public function getAllClassMethodDetails( $classMethods )
	{
		$classMethodDetails = [];
		foreach( $classMethods as $scope => $methods ) {
			if( !empty($methods) ) {
				$classMethodDetails[$scope] = $this->getMethodDetailsInScope( $scope, $methods );
			}
		}

		return $classMethodDetails;
	}

	/**
	 * Get the method details in a particular scope
	 *
	 * @param	$scope		string
	 * @param	$methods	array
	 * @return	array
	 */
	private function getMethodDetailsInScope( $scope, $methods )
	{
		$details = [];
		foreach( $methods as $key => $value ) {
			$methodDetails = $this->getMethodDetails( $scope, $value );
			array_push( $details, $methodDetails );
		}

		return $details;
	}

	/**
	 * Get the details for a method.
	 *
	 * @param	$scope		string
	 * @param	$method		\ReflectionMethod
	 * @return	MethodDetails
	 */
	private function getMethodDetails( $scope, \ReflectionMethod $method )
	{
		$details = new MethodDetails;
		$details->name = $method->getName();
		$details->scope = $scope;
		$details->isSetter = (stristr( $details->name, 'set' ) !== FALSE);
		$details->arguments = $this->argumentWorker->getAllMethodArgumentsDetails( $method );

		return $details;
	}

}

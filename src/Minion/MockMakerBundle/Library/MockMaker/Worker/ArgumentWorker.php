<?php

/**
 *	ArgumentWorker
 *
 *	Processes method arguments
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 22, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Worker;

use Minion\MockMakerBundle\Library\MockMaker\Model\ArgumentDetails;

use Minion\UnitTestBundle\Library\DebuggerMinion;

class ArgumentWorker
{

	/**
	 * Get the details for a method's arguments.
	 *
	 * @param	$method				\ReflectionMethod
	 * @return	ArgumentDetails
	 */
	//public function getArgumentDetails( \ReflectionParameter $argument )
	public function getAllMethodArgumentsDetails( \ReflectionMethod $method )
	{
		$details = [];
		$arguments = $method->getParameters();

		if( empty($arguments) ) {
			return $details;
		}

		foreach( $arguments as $k => $arg ) {
			array_push( $details, $this->getArgumentDetails( $arg ) );
		}

		return $details;
	}

	/**
	 * Get the details for a single method argument
	 *
	 * @param	$argument	\ReflectionParameter
	 * @return	ArgumentDetails
	 */
	private function getArgumentDetails( \ReflectionParameter $argument )
	{
		$details = new ArgumentDetails;
		$details->name = $argument->getName();
		$details->passedByReference = $argument->isPassedByReference();
		// true if no typehinting or typehinted argument defaults to null
		$details->allowsNull = $argument->allowsNull();
		$details->type = $this->getArgumentType( $argument );
		if( $details->type === 'object' ) {
			$details->typeHint = $this->getArgumentTypeHint( $argument->__toString() );
		}
		if( $argument->isOptional() ) {
			$details->isRequired = FALSE;
			$details->defaultValue = $argument->getDefaultValue();
		}

		return $details;
	}

	/**
	 * Get the argument data type.
	 *
	 * TODO: There are some values that cannot be obtained unless the PHP version is high enough:
	 *
	 * php >= 5.4
	 *  isCallable()
	 *  isDefaultValueConstant()
	 *  getDefaultValueConstantName()
	 *
	 * php >= 5.6
	 *  isVariadic()	-> function sum(...$numbers) {} -> elipsis indicated method overloading
	 *
	 * @param	$argument	\ReflectionParameter
	 * @return	string
	 */
	private function getArgumentType( \ReflectionParameter $argument )
	{
		if( $argument->isArray() ) {
			return 'array';
		}

		// check to see if it's a typehinted class
		$regex = '/^.*\<\w+?> ([\w\\\\]+?) +.*$/';
		preg_match( $regex, $argument->__toString(), $matches );
		if( isset($matches[1]) ) {
			return 'object';
		}

		if( $argument->isOptional() ) {
			return gettype( $argument->getDefaultValue() );
		}

		return NULL;
	}

	/**
	 * Get the typehint string for an argument, if any.
	 *
	 * Gotchas:
	 * - If a typehinted argument can be set to null, the __toString() method
	 *   returns " or NULL" after the class name.
	 *
	 * @param	$type	string	The argument data type
	 * @return	string
	 */
	private function getArgumentTypeHint( $type )
	{
		$regex = '/^.*\<\w+?> ([\w\\\\]+?) +.*$/';
		preg_match( $regex, $type, $matches );
		if( isset( $matches[1] ) ) {
			return $this->formatTypeHintClass( $matches[1] );
		}

		return NULL;
	}

	/**
	 * Format the typehinted class name with a forward slash if required
	 * No \ in the string likely indicates a top level class i.e. Exception or DateTime
	 *
	 * @param	$class	string
	 * @return	string
	 */
	private function formatTypeHintClass( $class )
	{
		return (strpos( $class, '\\' ) === FALSE) ? "\\{$class}" : $class;
	}

}
<?php

/**
 *	MethodWorkerEntity
 *
 *	Entity for testing method worker.
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Resources\Entities;

use Minion\MockMakerBundle\Tests\Resources\Entities\SimpleEntity;

class MethodWorkerEntity
{

	/**
	 * Public methods
	 */

	public function noArguments()
	{
		return true;
	}

	public function setSomeProperty( $setSomePropertyArgument )
	{
		return true;
	}

	public function oneArgumentNoTypehint( $oneArgumentNoTypehintArgument )
	{
		return $oneArgumentNoTypehintArgument;
	}

	public function twoArgumentsNoTypehint( $argument1, $argument2 )
	{
		return $argument1 . "::" . $argument2;
	}

	public function twoArgumentsOneOptional( $argument1, $argument2 = 'defaultArgument2Value' )
	{
		return $argument1 . "::" . $argument2;
	}

	public function oneArgumentDefaultNull( $argument = null )
	{
		return $argument;
	}

	public function oneArgumentDefaultBoolFalse( $argument = false )
	{
		return $argument;
	}

	public function oneArgumentDefaultBoolTrue( $argument = true )
	{
		return $argument;
	}

	public function oneArgumentDefaultString( $argument = 'argumentDefaultValue' )
	{
		return $argument;
	}

	public function oneArgumentTypehintedException( \Exception $exception )
	{
		return $simpleEntity->publicProperty;
	}

	public function oneArgumentTypehinted( SimpleEntity $simpleEntity )
	{
		return $simpleEntity->publicProperty;
	}

	public function oneArgumentTypehintedWithDefaultNull( SimpleEntity $simpleEntity = null )
	{
		return $simpleEntity->publicProperty;
	}

	public function twoArgumentsOneTypehintedOneDefaultNull( SimpleEntity $simpleEntity, $argument2 = null )
	{
		return $simpleEntity->publicProperty . "::" . $argument2;
	}

	/**
	 * Private methods
	 */

	private function privateNoArguments()
	{
		return true;
	}

	private function privateOneArgumentNoTypehint( $argument )
	{
		return $argument;
	}

	private function privateTwoArgumentsNoTypehint( $argument1, $argument2 )
	{
		return $argument1 . "::" . $argument2;
	}

	private function privateTwoArgumentsOneOptional( $argument1, $argument2 = 'defaultArgument2Value' )
	{
		return $argument1 . "::" . $argument2;
	}

	private function privateOneArgumentDefaultNull( $argument = null )
	{
		return $argument;
	}

	private function privateOneArgumentDefaultBoolFalse( $argument = false )
	{
		return $argument;
	}

	private function privateOneArgumentDefaultBoolTrue( $argument = true )
	{
		return $argument;
	}

	private function privateOneArgumentDefaultString( $argument = 'argumentDefaultValue' )
	{
		return $argument;
	}

	private function privateOneArgumentTypehinted( SimpleEntity $simpleEntity )
	{
		return $simpleEntity->publicProperty;
	}

	private function privateTwoArgumentsOneTypehintedOneDefaultNull( SimpleEntity $simpleEntity, $argument2 = null )
	{
		return $simpleEntity->publicProperty . "::" . $argument2;
	}


	/**
	 * Protected methods
	 */

	protected function protectedNoArguments()
	{
		return true;
	}

	protected function protectedOneArgumentNoTypehint( $argument )
	{
		return $argument;
	}

	protected function protectedTwoArgumentsNoTypehint( $argument1, $argument2 )
	{
		return $argument1 . "::" . $argument2;
	}

	protected function protectedTwoArgumentsOneOptional( $argument1, $argument2 = 'defaultArgument2Value' )
	{
		return $argument1 . "::" . $argument2;
	}

	protected function protectedOneArgumentDefaultNull( $argument = null )
	{
		return $argument;
	}

	protected function protectedOneArgumentDefaultBoolFalse( $argument = false )
	{
		return $argument;
	}

	protected function protectedOneArgumentDefaultBoolTrue( $argument = true )
	{
		return $argument;
	}

	protected function protectedOneArgumentDefaultString( $argument = 'argumentDefaultValue' )
	{
		return $argument;
	}

	protected function protectedOneArgumentTypehinted( SimpleEntity $simpleEntity )
	{
		return $simpleEntity->publicProperty;
	}

	protected function protectedTwoArgumentsOneTypehintedOneDefaultNull( SimpleEntity $simpleEntity, $argument2 = null )
	{
		return $simpleEntity->publicProperty . "::" . $argument2;
	}

}

<?php

/**
 *	TestEntity
 *
 *	For use in testing MockMaker
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 18, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Resources\Entities;

class TestEntity
{
	const CONSTANT_PROPERTY1 = 'constProperty1 value';
	const CONSTANT_PROPERTY2 = 'constProperty2 value';
	//const CONSTANT_PROPERTY3 = 'constProperty3 value';
	//const CONSTANT_PROPERTY4 = 'constProperty4 value';

	// public bicycle
	public $publicProperty1 = 'publicProperty1 value';
	public $publicProperty2 = 'publicProperty2 value';
	public $publicProperty3 = 'publicProperty3 value';
	public $publicProperty4 = 'publicProperty4 value';

	// only this class
	private $privateProperty1 = 'privateProperty1 value';
	private $privateProperty2 = 'privateProperty2 value';
	private $privateProperty3 = 'privateProperty3 value';
	private $privateProperty4 = 'privateProperty4 value';

	// this class and it's children
	protected $protectedProperty1 = 'protectedProperty1 value';
	protected $protectedProperty2 = 'protectedProperty2 value';
	protected $protectedProperty3 = 'protectedProperty3 value';
	protected $protectedProperty4 = 'protectedProperty4 value';

	public static $publicStaticProperty1;
	public static $publicStaticProperty2;
	public static $publicStaticProperty3;
	public static $publicStaticProperty4;

	private static $privateStaticProperty1;
	private static $privateStaticProperty2;
	private static $privateStaticProperty3;
	private static $privateStaticProperty4;

	protected static $protectedStaticProperty1;
	protected static $protectedStaticProperty2;
	protected static $protectedStaticProperty3;
	protected static $protectedStaticProperty4;

	public function getPublicProperty1()
	{
		return $this->publicProperty1;
	}
	public function getPublicProperty2()
	{
		return $this->publicProperty2;
	}
	public function setPublicProperty1( $publicProperty1 )
	{
		$this->publicProperty1 = $publicProperty1;
	}
	public function setPublicProperty2( $publicProperty2 )
	{
		$this->publicProperty2 = $publicProperty2;
	}

	public function getProtectedProperty1()
	{
		return $this->protectedProperty1;
	}
	public function getProtectedProperty2()
	{
		return $this->protectedProperty2;
	}
	public function setProtectedProperty1( $protectedProperty1 )
	{
		$this->protectedProperty1 = $protectedProperty1;
	}
	public function setProtectedProperty2( $protectedProperty2 )
	{
		$this->protectedProperty2 = $protectedProperty2;
	}

	public function getPrivateProperty1()
	{
		return $this->protectedProperty1;
	}
	public function getPrivateProperty2()
	{
		return $this->protectedProperty2;
	}


}
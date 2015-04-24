<?php

/**
 *	PropertyWorkerEntity
 *
 *	Used to test the PropertyWorker
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Tests\Resources\Entities;

class PropertyWorkerEntity
{

	const constProperty1 = 'constProperty1 value';
	const constProperty2 = 'constProperty2 value';
	const constProperty3 = 'constProperty3 value';
	const constProperty4 = 'constProperty4 value';

	public $publicProperty1 = 'publicProperty1 value';
	public $publicProperty2 = 'publicProperty2 value';
	public $publicProperty3 = 'publicProperty3 value';
	public $publicProperty4 = 'publicProperty4 value';

	private $privateProperty1 = 'privateProperty1 value';
	private $privateProperty2 = 'privateProperty2 value';
	private $privateProperty3 = 'privateProperty3 value';
	private $privateProperty4 = 'privateProperty4 value';

	protected $protectedProperty1 = 'protectedProperty1 value';
	protected $protectedProperty2 = 'protectedProperty2 value';
	protected $protectedProperty3 = 'protectedProperty3 value';
	protected $protectedProperty4 = 'protectedProperty4 value';

	public static $publicStaticProperty1;
	public static $publicStaticProperty2;

	private static $privateStaticProperty1;
	private static $privateStaticProperty2;

	protected static $protectedStaticProperty1;
	protected static $protectedStaticProperty2;

}
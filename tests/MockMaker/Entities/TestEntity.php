<?php

/**
 * 	TestEntity
 *
 * 	For use in testing MockMaker
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 18, 2015
 * 	@version	1.0
 */

namespace MockMaker\Entities;

use MockMaker\Entities\SimpleEntity;
use stdClass;

class TestEntity
{

    // const can only be public
    const CONSTANT_PROPERTY1 = 'constProperty1 value';
    const CONSTANT_PROPERTY2 = 'constProperty2 value';

    // public bicycle
    public $publicProperty1 = 'publicProperty1 value';
    public $publicProperty2 = 'publicProperty2 value';
    public $publicPropertyDefaultValueAssignedByConstructor;
    public $publicPropertyDefaultValueAssignedByConstructorTopLevelClass;
    public static $publicStaticProperty1 = 'publicStaticProperty1 value';
    public static $publicStaticProperty2 = 'publicStaticProperty2 value';
    // only this class
    private $privateProperty1 = 'privateProperty1 value';
    private $privateProperty2 = 'privateProperty2 value';
    private $privatePropertyDefaultValueAssignedByConstructor;
    private $privatePropertyDefaultValueAssignedByConstructorTopLevelClass;
    private static $privateStaticProperty1 = 'privateStaticProperty1 value';
    private static $privateStaticProperty2 = 'privateStaticProperty2 value';
    // this class and it children
    protected $protectedProperty1 = 'protectedProperty1 value';
    protected $protectedProperty2 = 'protectedProperty2 value';
    protected $protectedPropertyDefaultValueAssignedByConstructor;
    protected $protectedPropertyDefaultValueAssignedByConstructorTopLevelClass;
    protected static $protectedStaticProperty1 = 'protectedStaticProperty1 value';
    protected static $protectedStaticProperty2 = 'protectedStaticProperty2 value';

    public function __construct()
    {
        $this->publicPropertyDefaultValueAssignedByConstructor = new SimpleEntity();
        $this->publicPropertyDefaultValueAssignedByConstructorTopLevelClass = new \DateTime();
        $this->privatePropertyDefaultValueAssignedByConstructor = new SimpleEntity();
        $this->privatePropertyDefaultValueAssignedByConstructorTopLevelClass = new \DateTime();
        $this->protectedPropertyDefaultValueAssignedByConstructor = new SimpleEntity();
        $this->protectedPropertyDefaultValueAssignedByConstructorTopLevelClass = new stdClass();
    }

    public function getPublicProperty1()
    {
        return $this->publicProperty1;
    }

    public function getPublicProperty2()
    {
        return $this->publicProperty2;
    }

    public function setPublicProperty1($publicProperty1)
    {
        $this->publicProperty1 = $publicProperty1;
    }

    public function setPublicProperty2($publicProperty2)
    {
        $this->publicProperty2 = $publicProperty2;
    }

    public function setPublicTypehintedProperty(SimpleEntity $simpleEntity)
    {
        $this->publicTypehintedProperty = $simpleEntity;
    }

    public function getProtectedProperty1()
    {
        return $this->protectedProperty1;
    }

    public function getProtectedProperty2()
    {
        return $this->protectedProperty2;
    }

    public function setProtectedProperty1($protectedProperty1)
    {
        $this->protectedProperty1 = $protectedProperty1;
    }

    public function setProtectedProperty2($protectedProperty2)
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

    public function thisMethodIsNotARealSetter()
    {
        return __METHOD__;
    }

    public function gettingUpsetIfThisQualifiesAsASetter()
    {
        return __METHOD__;
    }

}

<?php

/**
 *	PropertyWorker
 *
 *	@author		Evan Johnson
 *	@created	Apr 19, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Worker;

use Minion\MockMakerBundle\Library\MockMaker\Model\PropertyDetails;
use Minion\MockMakerBundle\Library\MockMaker\Exception;
use Minion\UnitTestBundle\Library\DebuggerMinion;

class PropertyWorker
{

	/**
	 * Class methods
	 *
	 * @var	array
	 */
	protected $classMethods;

	/**
	 * Instance of the class.
	 *
	 * @var	object
	 */
	protected $classInstance;

	/**
	 * Reflection class
	 *
	 * @var	\ReflectionClass
	 */
	protected $reflectionClass;

	/**
	 * Provide required data to process the class's properties.
	 *
	 * @param	$methods		array				Array of the class's methods.
	 * @param	$instance		object				Instance of the class.
	 * @param	$reflection		\ReflectionClass	The \ReflectionClass instance of the class.
	 * @throws	\InvalidArgumentException
	 */
	public function __construct( $methods, $instance, \ReflectionClass $reflection )
	{
		$this->classMethods = $methods;
		if( !is_object($instance) ) {
			throw new \InvalidArgumentException( Exception\MockMakerErrors::PW_INVALID_CLASS_INSTANCE );
		}
		$this->classInstance = $instance;
		$this->reflectionClass = $reflection;
	}

	/**
	 * Get details for all class properties.
	 *
	 * @param	$classProperties	array	Array of \ReflectionProperties
	 * @return	array
	 */
	public function getAllClassPropertyDetails( $classProperties )
	{
		$classPropertyDetails = [];
		foreach( $classProperties as $scope => $properties ) {
			if( !empty($properties) ) {
				$classPropertyDetails[$scope] = [];
				/**
				 * constant are a problem here, as it's an associative array, not numeric
				 * so we have to use key => value instead of just the element
				 */
				foreach( $properties as $key => $value ) {
					$propDetails = $this->getPropertyDetails( $scope, $key, $value );
					array_push( $classPropertyDetails[$scope], $propDetails );
				}
			}
		}
		$this->setIsStaticFlagsOnProperties( $classPropertyDetails );

		return $classPropertyDetails;
	}

	/**
	 * Get details for a property.
	 *
	 * @param	$scope	string		Public/private/protected/static/etc
	 * @param	$key	mixed		String or int array key
	 * @param	$val	mixed		String or \ReflectionProperty object
	 * @return	array
	 */
	private function getPropertyDetails( $scope, $key, $val )
	{
		$return = new PropertyDetails;
		$return->name = $this->getPropertyName( $key, $val );
		$return->scope = $scope;
		if( in_array( $scope, array('constant', 'public', 'static') ) ) {
			$return->defaultValue = $this->getPropertyValue( $val );
		}
		if( in_array( $scope, array('private', 'protected') ) ) {
			$refProp = $this->reflectionClass->getProperty($return->name);
			$refProp->setAccessible(true);
			$return->defaultValue = $this->getPropertyValue( $refProp );
		}
		$return->setter = $this->findPropertySetterIfExists( $return->name );
		$return->type = gettype( $return->defaultValue );
		if( is_object($return->defaultValue) ) {
			$return->typeHint = $this->formatTypeHint( get_class($return->defaultValue) );
		}

		return $return;
	}

	/**
	 * Get the name of a property's setter, if one exists.
	 *
	 * This method has to make the assumption that a property's
	 * setter will be named after it. E.g. a property called "totalCount"
	 * has a setter with the name "setTotalCount".
	 *
	 * @param	$name	string	Property name
	 * @return	mixed
	 */
	private function findPropertySetterIfExists( $name )
	{
		foreach( $this->classMethods as $scope => $methods ) {
			if( !empty($methods) ) {
				foreach( $methods as $k => $method ) {
					if( stripos( $method->name, "set{$name}" ) !== FALSE ) {
						return $method->name;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Add a \ to top level classes
	 *
	 * @param	$name	string
	 * @return	string
	 */
	private function formatTypeHint( $name )
	{
		if( strpos( $name, '\\' ) === FALSE ) {
			return "\\{$name}";
		}

		return $name;
	}

	/**
	 * Get the property name from each found property.
	 *
	 * @param	$key	mixed
	 * @param	$value	mixed
	 * @return	string
	 */
	private function getPropertyName( $key, $value )
	{
		if( $value instanceof \ReflectionProperty ) {
			return $value->name;
		}

		return $key;
	}

	/**
	 * Get the property value from each found property.
	 *
	 * @param	$value	mixed
	 * @return	string
	 */
	private function getPropertyValue( $value )
	{
		if( $value instanceof \ReflectionProperty ) {
			// this will throw an exception if it's private/protected, so we have to check.
			if( $value->isPrivate() || $value->isProtected() ) {
				$value->setAccessible( TRUE );
			}
			return (is_object($this->classInstance)) ? $value->getValue($this->classInstance) : 'unknown';
		}

		return $value;
	}

	/**
	 * Once we've got all the properties processed, we need to
	 * go back and assign the isStatic flag to the varous scope types.
	 *
	 * Warning! Pass by reference!
	 *
	 * @param	$classProperties	array
	 * @return	array
	 */
	private function setIsStaticFlagsOnProperties( &$classProperties )
	{
		if( !isset( $classProperties['static'] ) || empty( $classProperties['static'] ) ) {
			return;
		}
		$staticProps = $classProperties['static'];
		unset($classProperties['static']);
		$staticPropsNameArr = [];
		foreach( $staticProps as $key => $details ) {
			$staticPropsNameArr[$details->name] = true;
		}
		foreach( $classProperties as $scope => $properties ) {
			foreach( $properties as $key => $property ) {
				if( isset( $staticPropsNameArr[$property->name] ) ) {
					$property->isStatic = true;
				}
			}
		}
	}

}

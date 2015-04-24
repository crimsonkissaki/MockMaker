<?php

/**
 *	TestMinion
 *
 *	Proxy for simple access to various utility classes.
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 1, 2015
 *	@version	1.0
 */

namespace Minion\UnitTestBundle\Library;

use Minion\UnitTestBundle\Library\DebuggerMinion;
use Minion\UnitTestBundle\Library\ReflectionMinion;

class TestMinion
{

	/**
	 * Should return root bundle path.
	 *
	 * @return	string
	 */
	public static function getRootBundlePath()
	{
		return dirname( dirname( __FILE__ ) );
	}

	/**
	 * Get the path to the root project directory.
	 *
	 * @return type
	 */
	public static function getRootProjectPath()
	{
		return dirname( dirname( __FILE__ ) );
	}

	/**
	 * Should return the path to src/RAPP/Bundle/LoyaltyBundle/Tests/Mocks/Resources
	 *
	 * @return	string
	public static function getMockResourcesPath()
	{
		return self::getRootBundlePath() . '/Tests/Mocks/Resources/';
	}
	 */

	/**
	 * Passthrough to DebuggerMinion::dbug
	 *
	 * Use to output variables to terminal or browser
	 * along with descriptions and optional fatality.
	 *
	 * This should also be able to handle  a single Doctrine entity
	 * without dumping an obscene amount of recursion crap on the screen.
	 * If you have a doctrine entity as part of another object (i.e. oneToMany entities)
	 * it still gets everything dumped for the child entity properties. :(
	 *
	 * @param	mixed	$var	Variable to debug
	 * @param	string	$txt	Text to output with debug (optional)
	 * @param	bool	$die	To kill, or not to kill (optional)
	 * @param	string	$eol	Use <BR> or \n for browser or terminal output (optional)
	 * @return	stdOutput
	 */
	public static function dbug( $var, $txt=null, $die=false, $eol="\n" )
	{
		DebuggerMinion::dbug( $var, $txt=null, $die=false, $eol="\n" );
		if( $die ) { die(); }
	}

	/**
	 * Passthrough to DebuggerMinion::oneLine
	 *
	 * Quick one liner variable inspection.
	 *
	 * @param	$var	mixed	Variable to debug
	 * @param	$txt	string	Output text preceding debug (optional)
	 * @param	$eol	string	Use <BR> or \n for browser or terminal output (optional)
	 */
	public static function oneLine( $var, $txt=null, $eol="\n" )
	{
		DebuggerMinion::oneLine( $var, $txt, $eol );
	}

	/**
	 * Passthrough to ReflectionMinion::setNonPublicValue
	 *
	 * This uses reflection to set the value of a private/protected parameter
	 * to a desired value.
	 *
	 * Unless you're changing the values of a Mockery/Prophecy created object,
	 * you can usually pass the same object as the first 2 parameters.
	 *
	 * Warning! Uses pass-by-reference!
	 *
	 * @param	$objToChange		object	Object whose property you want to change.
	 * @param	$objToReflect		mixed	Object to use as a base for inspecting the property.
	 * @param	$property			string	Name of the property you want to change.
	 * @param	$value				string	Value to set the property to.
	 */
	public static function setNonPublicValue( &$objToChange, $objToReflect, $property, $value )
	{
		ReflectionMinion::setNonPublicValue( $objToChange, $objToReflect, $property, $value );
	}

	/**
	 * Passthrough to ReflectionMinion::getAccessibleNonPublicMethod
	 *
	 * Make non-public methods accessible.
	 *
	 * @param	$methodName	string	Method you want to make public
	 * @return	\ReflectionMethod
	 */
	public static function getAccessibleNonPublicMethod( $methodName )
	{
		return ReflectionMinion::getAccessibleNonPublicMethod( $methodName );
	}

}

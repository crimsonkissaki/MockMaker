<?php

/**
 * 	TestHelper
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 26, 2015
 * 	@version	1.0
 */

namespace MockMaker\TestHelper;

class TestHelper
{

    /**
     * Returns the path to the root directory
     *
     * @return  string
     */
    public static function getRootDir()
    {
        return dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    }

    /**
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
    public static function dbug($var, $txt = null, $die = false, $eol = "\n")
    {
        if (stristr($eol, 'br') !== FALSE) {
            echo "<PRE>";
        }
        echo "{$eol}{$eol}-------------------------{$eol}";
        if (!is_null($txt)) {
            echo "$txt{$eol}{$eol}";
        }
        echo "Var is type: " . gettype($var) . "{$eol}{$eol}";
        switch (TRUE) {
            case ($var === FALSE):
                echo 'FALSE';
                break;
            case ($var === TRUE):
                echo 'TRUE';
                break;
            case ($var === null):
                echo 'NULL';
                break;
            case (is_object($var)):
                if ($var instanceof \Exception) {
                    echo $var->getTraceAsString();
                    break;
                }
                print_r($var);
                break;
            case (is_array($var)):
                print_r($var);
                break;
            default:
                echo $var;
                break;
        }
        echo "{$eol}-------------------------{$eol}{$eol}";
        if (stristr($eol, 'br') !== FALSE) {
            echo "<PRE>";
        }
        if ($die) {
            die();
        }
    }

    /**
     * Quick one liner variable inspection.
     *
     * @param	$var	mixed	Variable to debug
     * @param	$txt	string	Output text preceding debug (optional)
     * @param	$eol	string	Use <BR> or \n for browser or terminal output (optional)
     */
    public static function oneLine($var, $txt = null, $eol = "\n")
    {
        $string = (!is_null($txt) ) ? "{$txt} | " : "";
        $string .= 'var type:\'' . gettype($var) . '\' : value:\'';
        switch (TRUE) {
            case ($var === FALSE):
                $string .= 'FALSE';
                break;
            case ($var === TRUE):
                $string .= 'TRUE';
                break;
            case ($var === null):
                $string .= 'NULL';
                break;
            case (is_object($var)):
                $string .= 'OBJECT';
                break;
            case (is_array($var)):
                $string .= 'ARRAY';
                break;
            default:
                $string .= $var;
                break;
        }
        $string .= "'{$eol}";
        echo $string;
    }

    /**
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
    public static function setNonPublicValue(&$objToChange, $objToReflect, $property, $value)
    {
        $class = (is_object($objToReflect)) ? get_class($objToReflect) : $objToReflect;
        $refClass = new \ReflectionClass($class);
        $refProp = $refClass->getProperty($property);
        $refProp->setAccessible(true);
        $refProp->setValue($objToChange, $value);
    }

    /**
     * Make non-public methods accessible.
     *
     * $actual = $method->invoke( <class>, <param1>, [<param2>...] );
     *
     * @param	$class			string	Class you want to access method in.
     * @param	$methodName		string	Method you want to make public.
     * @return	\ReflectionMethod
     */
    public static function getAccessibleNonPublicMethod($class, $methodName)
    {
        $className = (is_object($class)) ? get_class($class) : $class;
        $reflection = new \ReflectionClass($className);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(TRUE);

        return $method;
    }

}

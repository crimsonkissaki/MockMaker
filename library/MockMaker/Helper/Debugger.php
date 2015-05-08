<?php
/**
 * Debugger
 *
 * Slightly better than normal debugging formatter.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/6/15
 * @version        1.0
 */

namespace MockMaker\Helper;

class Debugger
{

    /**
     * Use to output variables to terminal or browser
     * along with descriptions and optional fatality.
     *
     * This should also be able to handle  a single Doctrine entity
     * without dumping an obscene amount of recursion crap on the screen.
     * If you have a doctrine entity as part of another object (i.e. oneToMany entities)
     * it still gets everything dumped for the child entity properties. :(
     *
     * @param    mixed  $var Variable to debug
     * @param    string $txt Text to output with debug (optional)
     * @param    bool   $die To kill, or not to kill (optional)
     * @param    string $eol Use <BR> or \n for browser or terminal output (optional)
     * @return   string
     */
    public static function dbug($var, $txt = null, $die = false, $eol = "\n")
    {
        if (stristr($eol, 'br') !== false) {
            echo "<PRE>";
        }
        echo "{$eol}{$eol}-------------------------{$eol}";
        if (!is_null($txt)) {
            echo "$txt{$eol}{$eol}";
        }
        echo "Var is type: " . gettype($var) . "{$eol}{$eol}";
        switch (true) {
            case ($var === false):
                echo 'FALSE';
                break;
            case ($var === true):
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
        if (stristr($eol, 'br') !== false) {
            echo "<PRE>";
        }
        if ($die) {
            die();
        }
    }

    /**
     * Quick one liner variable inspection.
     *
     * @param    $var    mixed    Variable to debug
     * @param    $txt    string    Output text preceding debug (optional)
     * @param    $eol    string    Use <BR> or \n for browser or terminal output (optional)
     */
    public static function oneLine($var, $txt = null, $eol = "\n")
    {
        $string = (!is_null($txt)) ? "{$txt} | " : "";
        $string .= 'var type:\'' . gettype($var) . '\' : value:\'';
        switch (true) {
            case ($var === false):
                $string .= 'FALSE';
                break;
            case ($var === true):
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
}
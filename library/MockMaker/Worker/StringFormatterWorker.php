<?php

/**
 * StringFormatterWorker
 *
 * Used by various classes to format strings with delineated values.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 20, 2015
 * @version        1.0
 */

namespace MockMaker\Worker;

class StringFormatterWorker
{

    /**
     * Interpolates an associative array of values into a string
     *
     * Looks for sub strings in $str that match a format of
     * $char . $key . $char
     *
     * @param    string $str  String to insert data into
     * @param    array  $vars Substring values and data
     * @param    string $char Delimiter character
     * @return    string
     */
    public static function vsprintf2($str = '', $vars = [], $char = '%')
    {
        if (empty($str)) {
            return '';
        }
        if (count($vars) > 0) {
            foreach ($vars as $k => $v) {
                if (!is_array($v)) {
                    $needle = "{$char}{$k}{$char}";
                    $txt = (is_null($v)) ? 'NULL' : $v;
                    $str = str_replace($needle, $txt, $str);
                }
            }
        }

        return $str;
    }
}

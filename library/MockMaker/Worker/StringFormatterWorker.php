<?php

/**
 * 	StringFormatterWorker
 *
 * 	Used by various classes to format strings with delineated values.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 20, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

class StringFormatterWorker
{

    /**
     * Interpolate an associative array of values into a string.
     * Placeholders in string must === the array keys.
     *
     * @param	string	$str
     * @param	array	$vars
     * @param	string	$char
     * @return	string
     */
    public static function vsprintf2($str = false, $vars = [ ], $char = '%')
    {
        if (!$str) {
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

    /**
     * Format (single|array of) directory paths.
     *
     * @param   $paths  array
     * @return  mixed
     */
    public static function formatDirectoryPaths($paths)
    {
        if (is_array($paths)) {
            $fixed = [ ];
            foreach ($paths as $key => $path) {
                $fixed[$key] = self::formatDirectoryPath($path);
            }
            return $fixed;
        } else {
            return self::formatDirectoryPath($paths);
        }
    }

    /**
     * Format a directory path.
     *
     * @param   $path   string
     * @return  string
     */
    public static function formatDirectoryPath($path)
    {
        $fixedPath = trim($path);
        if (substr($fixedPath, -1) !== '/') {
            $fixedPath .= '/';
        }

        return $fixedPath;
    }

}

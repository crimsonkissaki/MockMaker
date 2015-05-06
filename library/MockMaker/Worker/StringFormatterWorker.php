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

    /**
     * Formats directory paths
     *
     * trim()s whitespace and appends a forward slash.
     *
     * @param   string|array $paths Directory paths to format
     * @return  string|array
     */
    public static function formatDirectoryPaths($paths)
    {
        if (is_array($paths)) {
            $fixed = [];
            foreach ($paths as $key => $path) {
                $fixed[$key] = self::formatDirectoryPath($path);
            }

            return $fixed;
        } else {
            return self::formatDirectoryPath($paths);
        }
    }

    /**
     * Formats a directory path
     *
     * @param   string $path Directory path to format
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

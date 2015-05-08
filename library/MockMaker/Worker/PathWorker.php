<?php
/**
 * PathWorker
 *
 * Handles file path manipulations
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/6/15
 * @version        1.0
 */

namespace MockMaker\Worker;

class PathWorker
{

    /**
     * Gets the final element in a delimited string
     *
     * Good for paths and namespace manipulation.
     *
     * @param   string $path  Path to parse
     * @param   string $delim String to use to find element
     * @return  string
     */
    public static function getLastElementInPath($path, $delim = '/')
    {
        return join('', array_slice(explode($delim, $path), -1));
    }

    /**
     * Gets the class name from a fully qualified path
     *
     * @param   string $path
     * @return  string
     */
    public static function getClassNameFromFilePath($path)
    {
        $fileName = self::getLastElementInPath($path);

        return str_replace('.php', '', $fileName);
    }

    /**
     * Converts a fully qualified file path to a namespace path
     *
     * @param   string $filePath Fully qualified file path
     * @param   string $rootPath Project root path
     * @return  string
     */
    public static function convertFilePathToClassPath($filePath, $rootPath)
    {
        // simplify things by removing the root dir path from the file name
        $shortPath = str_replace($rootPath, '', $filePath);
        // remove extension and beginning / if present
        $basePath = trim(str_replace('.php', '', $shortPath), '/');
        // attempt to resolve PSR-0 and PSR-4 namespaces
        $classPath = str_replace(array(DIRECTORY_SEPARATOR, '_'), '\\', $basePath);

        return $classPath;
    }

    /**
     * Gets the path up to a final element
     *
     * @param   string $path
     * @param   string $delim
     * @return  string
     */
    public static function getPathUpToName($path, $delim = '/')
    {
        $return = '';
        if (($lastDelim = strrpos($path, $delim)) !== false) {
            $return = substr($path, 0, $lastDelim);
        }

        return $return;
    }

    /**
     * Gets the relative path between two paths
     *
     * @param   string $parentPath Parent (higher-level) path
     * @param   string $childPath  Child (lower-level) path
     * @return  string
     */
    public static function findRelativePath($parentPath, $childPath)
    {
        $parent = self::formatDirectoryPath($parentPath);
        $child = self::formatDirectoryPath($childPath);

        return ($parent === $child) ? '' : str_replace($parent, '', $child);
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
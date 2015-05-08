<?php
/**
 * ComposerWorker
 *
 * This class is responsible for manipulating composer data
 * to get data we need when mocking files.
 *
 * @package    MockMaker
 * @author     Evan Johnson
 * @created    5/4/15
 */

namespace MockMaker\Worker;

class ComposerWorker
{

    /**
     * Uses composer autoload information in an attempt to get a valid namespace for a file.
     *
     * @param   string $fileName        File that needs a valid namespace
     * @param   string $projectRootPath Path to project root folder
     * @return  string
     */
    public function getNamespaceFromComposer($fileName, $projectRootPath)
    {
        $composerData = false;

        // manual check for the vendor root dir in case project root path was overridden
        if (($vendorPos = strpos(__FILE__, 'vendor')) !== false) {
            $projectRootPath = substr(__FILE__, 0, $vendorPos);
        }
        $composerAutoloadFile = $projectRootPath . 'vendor/autoload.php';
        if (!is_file($composerAutoloadFile)) {
            return $composerData;
        }
        $composer = include($composerAutoloadFile);
        $psr4NamespaceMaps = $composer->getPrefixesPsr4();
        $composerData = $this->checkForNamespace($fileName, $psr4NamespaceMaps);

        // no PSR-4 namespace found, attempt PSR-0
        if (!$composerData) {
            $psr0NamespaceMaps = $composer->getPrefixes();
            $composerData = $this->checkForNamespace($fileName, $psr0NamespaceMaps);
        }

        return $composerData;
    }

    /**
     * Parses through an array of composer namespace data to find a matching namespace for a file.
     *
     * @param   string $fileName
     * @param   array  $psrNamespaceMaps
     * @return  array|bool
     */
    private function checkForNamespace($fileName, $psrNamespaceMaps)
    {
        $composerData = false;
        foreach ($psrNamespaceMaps as $namespace => $directory) {
            foreach ($directory as $dir) {
                if (strstr($fileName, $dir) !== false) {
                    $composerData = array(
                        'namespace' => $namespace,
                        'path'      => $dir
                    );
                }
            }
        }

        return $composerData;
    }
}


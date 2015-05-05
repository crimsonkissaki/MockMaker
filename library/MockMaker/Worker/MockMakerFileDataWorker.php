<?php

/**
 * MockMakerFileDataWorker
 *
 * This class handles processing operations for the MockMakerFileData model.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 28, 2015
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerFileData;
use MockMaker\Model\ConfigData;
use MockMaker\Worker\StringFormatterWorker;
use MockMaker\Worker\ComposerWorker;
use MockMaker\Helper\TestHelper;

class MockMakerFileDataWorker
{

    /**
     * Creates & populates a new MockMakerFileData object
     *
     * @param   string     $file   Fully qualified file path of file to be mocked
     * @param   ConfigData $config ConfigData object
     * @return  MockMakerFileData
     */
    public function generateNewObject($file, ConfigData $config)
    {
        $fileName = $this->getFileName($file);
        $mockFileSavePath = $this->determineMockFileSavePath($file, $config);
        $obj = new MockMakerFileData();
        $obj->setProjectRootPath($config->getProjectRootPath())
            ->setSourceFileFullPath($file)
            ->setSourceFileName($fileName)
            ->setMockWriteDirectory($config->getMockWriteDirectory())
            ->setMockFileBaseNamespace($config->getMockFileBaseNamespace())
            ->setOverwriteExistingFiles($config->getOverwriteExistingFiles())
            ->setMockFileNameFormat($config->getMockFileNameFormat())
            ->setMockFileName($this->generateMockFileName($fileName, $config->getMockFileNameFormat()))
            ->setMockFileNamespace($this->generateMockFileNamespace($mockFileSavePath, $config))
            ->setMockFileSavePath($mockFileSavePath);

        return $obj;
    }

    /**
     * Gets the simple file name from a fully qualified file path
     *
     * @param   string $file  Fully qualified file path of file to be mocked
     * @param   string $delim String to use to find element
     * @return  string
     */
    private function getFileName($file, $delim = '/')
    {
        return join('', array_slice(explode($delim, $file), -1));
    }

    /**
     * Generates the mock file's name
     *
     * @param   string $fileName   Short file name
     * @param   string $nameFormat Format for mock file name
     * @return  string
     */
    private function generateMockFileName($fileName, $nameFormat)
    {
        $args = array('FileName' => str_replace('.php', '', $fileName));

        return StringFormatterWorker::vsprintf2($nameFormat, $args) . '.php';
    }

    /**
     * Generates the mock file's namespace
     *
     * If a mockFileBaseNamespace is not defined, this will use composer
     * to try to find a valid one. Failing that, a wild-assed-guess is returned.
     *
     * @param   string     $mockFileSavePath    Fully qualified file save path of file to be mocked
     * @param   ConfigData $config              ConfigData object
     *                                          Required data points are:
     *                                          - getMockFileBaseNamespace()
     *                                          - getProjectRootPath()
     *                                          - getMockWriteDirectory()
     * @return  string
     */
    private function generateMockFileNamespace($mockFileSavePath, ConfigData $config)
    {
        $mockDirectory = ($config->getMockWriteDirectory())
            ? $config->getMockWriteDirectory()
            : $this->removeLastNameFromPath($mockFileSavePath);
        // WAG of last resort
        $wagNamespace = rtrim(str_replace('/', '\\', str_replace($config->getProjectRootPath(), '', $mockDirectory)),
            '\\');

        if ($config->getMockFileBaseNamespace()) {
            return $this->determineNamespaceWithBaseNamespace($mockFileSavePath, $config->getMockFileBaseNamespace());
        }

        $composerWorker = new ComposerWorker();
        $composerData = $composerWorker->getNamespaceFromComposer($mockFileSavePath, $config->getProjectRootPath());

        // no namespaces found in composer
        if (!$composerData) {
            return $wagNamespace;
        }

        $pathToMockFile = rtrim($this->removeLastNameFromPath($mockFileSavePath), '/');
        $pathWithoutNamespacePath = str_replace($composerData['path'], '', $pathToMockFile);
        $namespace = $composerData['namespace'] . str_replace('/', '\\', $pathWithoutNamespacePath);

        return $namespace;
    }

    /**
     * Generates a valid mock file namespace usign a user-supplied base
     *
     * @param   string $mockFileSavePath Fully qualified file save path of file to be mocked
     * @param   string $baseNamespace    User-supplied base namespace
     * @return  string
     */
    private function determineNamespaceWithBaseNamespace($mockFileSavePath, $baseNamespace)
    {
        $modifiedNamespace = $baseNamespace;
        $mockPath = $this->removeLastNameFromPath($mockFileSavePath);
        $lastBaseNamespaceDir = $this->getFileName($baseNamespace, '\\');

        if (($strPos = strpos($mockPath, $lastBaseNamespaceDir)) !== false) {
            $subStr = substr($mockPath, $strPos + (strlen($lastBaseNamespaceDir)));
            $modifiedNamespace = $baseNamespace . str_replace('/', '\\', $subStr);
        }

        return $modifiedNamespace;
    }

    /**
     * Determines the full save path for this mock file
     *
     * @param   string     $targetFile Fully qualified file path of file to be mocked
     * @param   ConfigData $config     ConfigData object
     * @return  string
     */
    private function determineMockFileSavePath($targetFile, ConfigData $config)
    {
        $fileName = $this->getFileName($targetFile);
        $mockFileName = $this->generateMockFileName($fileName, $config->getMockFileNameFormat());
        $targetFilePath = $this->removeLastNameFromPath($targetFile);

        if ($config->getMockWriteDirectory()) {
            $mockFilePath = $config->getMockWriteDirectory() . $mockFileName;

            // this is only really required if we're reading a directory for files
            $readDirs = $config->getReadDirectories();

            if (empty($readDirs) || !$config->getPreserveDirectoryStructure()) {
                return $mockFilePath;
            }

            $targetOriginDir = false;
            foreach ($readDirs as $k => $readDir) {
                if (strpos($targetFile, $readDir) !== false) {
                    $targetOriginDir = $readDir;
                }
            }

            if (!$targetOriginDir) {
                return $mockFilePath;
            }

            // ok, now we know the origin directory for this file we're working on
            // we need to find the relative path to the file from this base file
            $relativeTargetFilePath = ($targetFilePath . '/' === $targetOriginDir)
                ? '' : str_replace($targetOriginDir, '', $targetFilePath);
            $mockFilePath = $config->getMockWriteDirectory()
                . ((empty($relativeTargetFilePath)) ? '' : $relativeTargetFilePath . '/')
                . $mockFileName;

            return $mockFilePath;
        }

        // no read directory specified - best guess
        return $targetFilePath . $mockFileName;
    }

    /**
     * Returns a full path without the last value after /
     *
     * @param   string $path  Path to strip the final /value from
     * @param   string $delim String delimiter to separate on
     * @return  string
     */
    private function removeLastNameFromPath($path, $delim = '/')
    {
        return substr($path, 0, strrpos($path, $delim));
    }
}


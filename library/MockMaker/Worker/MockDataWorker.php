<?php
/**
 * MockDataWorker
 *
 * Handles all MockData object data assignments
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/7/15
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;
use MockMaker\Model\EntityData;
use MockMaker\Model\MockData;

class MockDataWorker
{

    /**
     * Generates a new MockData object will all required information for creating mock files
     *
     * @param   EntityData $entity
     * @param   ConfigData $config
     * @return  MockData
     */
    public function generateMockDataObject(EntityData $entity, ConfigData $config)
    {
        $className = $this->formatMockClassName($entity->getClassName(), $config->getMockFileNameFormat());
        $savePath = $this->determineMockFileSavePath($entity->getFileData()->getFileDirectory(), $config);
        $utSavePath = $this->determineMockUnitTestFileSavePath($entity->getFileData()->getFileDirectory(), $config);
        $namespace = $this->generateMockFileNamespace($savePath, $config);
        $utNamespace = $this->generateMockFileNamespace($utSavePath, $config);
        $obj = new MockData();
        $obj->setClassName($className)
            ->setClassNamespace($namespace)
            ->setUtClassNamespace($utNamespace)
            ->getFileData()
            ->setFileName($className . '.php')
            ->setFileDirectory($savePath)
            ->setFullFilePath($savePath . $className . '.php');
        $obj->getUtFileData()
            ->setFileName($className . 'Test.php')
            ->setFileDirectory($utSavePath)
            ->setFullFilePath($utSavePath . $className . 'Test.php');
        $obj = $this->generateMockDataDetails($obj, $entity);

        return $obj;
    }

    /**
     * Gathers up all the fiddly details we need for the ClassData object
     *
     * @param   MockData   $obj
     * @param   EntityData $entity
     * @return  MockData
     */
    private function generateMockDataDetails(MockData $obj, EntityData $entity)
    {
        $methodWorker = new MethodDataWorker();
        $propertyWorker = new PropertyDataWorker();
        $targetClass = $entity->getClassNamespace() . '\\' . $entity->getClassName();

        $obj->setHasConstructor($entity->getHasConstructor())
            ->addUseStatements($entity->getUseStatements())
            ->setExtends($entity->getExtends())
            ->addImplements($entity->getImplements())
            ->addMethods($methodWorker->generateMethodObjects($entity->getMethods()))
            ->addProperties($propertyWorker->generatePropertyObjects($targetClass, $entity->getProperties(),
                $obj->getMethods()));

        return $obj;
    }

    /**
     * Formats the class name for the mock class
     *
     * @param   string $className Target class name
     * @param   string $format    Mock class name format string
     * @return  string
     */
    private function formatMockClassName($className, $format)
    {
        return StringFormatterWorker::vsprintf2($format, array('FileName' => $className));
    }

    /**
     * Determines the full save path for this mock file
     *
     * @param   string     $targetFileDir Fully qualified path of file to be mocked
     * @param   ConfigData $config        ConfigData object
     * @return  string
     */
    // needs mock file name, target file path, config
    private function determineMockFileSavePath($targetFileDir, ConfigData $config)
    {
        // no write directory specified - WAG it
        if (!$mockDir = $config->getMockWriteDir()) {
            return $targetFileDir;
        }

        return $this->tryForReadDirBasedPath($mockDir, $targetFileDir, $config);
    }

    /**
     * Determines the full save path for this mock file
     *
     * @param   string     $targetFileDir Fully qualified path of file to be mocked
     * @param   ConfigData $config        ConfigData object
     * @return  string
     */
    // needs mock file name, target file path, config
    private function determineMockUnitTestFileSavePath($targetFileDir, ConfigData $config)
    {
        // no write directory specified - WAG it
        if (!$utDir = $config->getUnitTestWriteDir()) {
            return $targetFileDir;
        }

        return $this->tryForReadDirBasedPath($utDir, $targetFileDir, $config);
    }

    /**
     * Attempts to determine a file path using the specified read directories
     *
     * @param   string     $mockDir       Directory where file should be saved
     * @param   string     $targetFileDir Path to target entity
     * @param   ConfigData $config        ConfigData object
     * @return  string
     */
    private function tryForReadDirBasedPath($mockDir, $targetFileDir, ConfigData $config)
    {
        // this is only really required if we're recursing dirs for files
        $readDirs = $config->getReadDirectories();
        if (empty($readDirs) || !$config->getPreserveDirStructure()) {
            return $mockDir;
        }

        if (!$targetOriginDir = $this->findTargetFileOriginDir($readDirs, $targetFileDir)) {
            return $mockDir;
        }

        $relPath = PathWorker::findRelativePath($targetOriginDir, $targetFileDir);

        return $mockDir . $relPath;
    }

    /**
     * Looks through the read directories to find a target file's origin
     *
     * This is required when recursively processing due to sub-folders.
     *
     * @param   array  $readDirs      Array of user-supplied read directories
     * @param   string $targetFileDir Path to target file
     * @return  string|bool
     */
    private function findTargetFileOriginDir(array $readDirs, $targetFileDir)
    {
        $targetOriginDir = false;
        foreach ($readDirs as $readDir) {
            if (strpos($targetFileDir, $readDir) !== false) {
                $targetOriginDir = $readDir;
                break;
            }
        }

        return $targetOriginDir;
    }

    /**
     * Generates the mock file's namespace
     *
     * If a mockFileBaseNamespace is not defined, this will use composer
     * to try to find a valid one. Failing that, a wild-assed-guess is returned.
     *
     * @param   string     $mockSavePath    Fully qualified file save path of file to be mocked
     * @param   ConfigData $config          ConfigData object
     *                                      Required data points are:
     *                                      - getMockFileBaseNamespace()
     *                                      - getProjectRootPath()
     *                                      - getMockWriteDir()
     * @return  string
     */
    private function generateMockFileNamespace($mockSavePath, ConfigData $config)
    {
        $wagNamespace = PathWorker::convertFilePathToClassPath($mockSavePath, $config->getProjectRootPath());

        if ($config->getMockFileBaseNamespace()) {
            return $this->determineWithBaseNamespace($mockSavePath, $config->getMockFileBaseNamespace());
        }

        $composerWorker = new ComposerWorker();
        $composerData = $composerWorker->getNamespaceFromComposer($mockSavePath, $config->getProjectRootPath());

        if (!$composerData) {
            return $wagNamespace;
        }

        $relNamespacePath = str_replace($composerData['path'], '', $mockSavePath);
        $namespace = $composerData['namespace'] . str_replace('/', '\\', $relNamespacePath);

        return rtrim($namespace, '\\');
    }

    /**
     * Generates a valid mock file namespace using a user-supplied base
     *
     * @param   string $mockPath      Fully qualified file save path of file to be mocked
     * @param   string $baseNamespace User-supplied base namespace
     * @return  string
     */
    private function determineWithBaseNamespace($mockPath, $baseNamespace)
    {
        $modifiedNamespace = $baseNamespace;
        $lastBaseNamespaceDir = PathWorker::getLastElementInPath($baseNamespace, '\\');
        if (($strPos = strpos($mockPath, $lastBaseNamespaceDir)) !== false) {
            $subStr = substr($mockPath, $strPos + (strlen($lastBaseNamespaceDir)));
            $modifiedNamespace = $baseNamespace . str_replace('/', '\\', $subStr);
        }

        return rtrim($modifiedNamespace, '\\');
    }
}
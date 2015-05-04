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
        $obj = new MockMakerFileData();
        $obj->setSourceFileFullPath($file)
            ->setSourceFileName($fileName)
            ->setMockFileName($this->generateMockFileName($fileName))
            ->setMockFileNamespace($this->generateMockFileNamespace($config))
            ->setProjectRootPath($config->getProjectRootPath())
            ->setMockWriteDirectory($config->getMockWriteDirectory())
            ->setMockFileSavePath($this->determineMockFileSavePath($file, $config))
            ->setOverwriteExistingFiles($config->getOverwriteExistingFiles());

        return $obj;
    }

    /**
     * Gets the simple file name from a fully qualified file path
     *
     * @param   string $file Fully qualified file path of file to be mocked
     * @return  string
     */
    private function getFileName($file)
    {
        return join('', array_slice(explode('/', $file), -1));
    }

    /**
     * Generates the mock file's name
     *
     * @param   string $fileName Short file name
     * @return  string
     */
    private function generateMockFileName($fileName)
    {
        $args = array('FileName' => rtrim($fileName, '.php'));

        return StringFormatterWorker::vsprintf2('%FileName%Mock.php', $args);
    }

    /**
     * Generates the mock file's namespace
     *
     * @param   ConfigData $config ConfigData object
     * @return  string
     */
    private function generateMockFileNamespace(ConfigData $config)
    {
        $trimmedPath = rtrim(str_replace($config->getProjectRootPath(), '', $config->getMockWriteDirectory()), '/');
        $namespace = str_replace('/', '\\', $trimmedPath);

        return $namespace;
    }

    private function determineMockFileSavePath($file, ConfigData $config)
    {
        if($config->getMockWriteDirectory()) {
            /**
             * ok, so basically we have 2 root directories:
             * the original file directory
             * and the file save path directory
             */
            //$filePath = $config->getMockWriteDirectory() . $mmFileData->getClassData()->getClassName() . 'Mock.php';
            return __METHOD__;
        }
    }

}


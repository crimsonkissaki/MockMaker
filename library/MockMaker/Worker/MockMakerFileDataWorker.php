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
            ->setProjectRootPath($config->getProjectRootPath())
            ->setMockWriteDirectory($config->getMockWriteDirectory());

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
}

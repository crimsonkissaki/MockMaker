<?php

/**
 * FileDataWorker
 *
 * This class handles processing operations for the FileData model.
 *
 * @package     MockMaker
 * @author		Evan Johnson
 * @created	    Apr 28, 2015
 * @version	    1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\FileData;
use MockMaker\Model\ConfigData;

class FileDataWorker
{

    /**
     * Creates & populates a new FileData object
     *
     * @param   string      $file       Fully qualified file path of file to be mocked
     * @param   ConfigData  $config     MockMakerConfig object
     * @return  FileData
     */
    public function generateNewObject($file, ConfigData $config)
    {
        $obj = new FileData();
        $obj->setFullFilePath($file)
            ->setFileName($this->getFileName($file))
            ->setProjectRootPath($config->getProjectRootPath());

        return $obj;
    }

    /**
     * Gets the simple file name from a fully qualified file path
     *
     * @param   string  $file   Fully qualified file path of file to be mocked
     * @return  string
     */
    private function getFileName($file)
    {
        return join('', array_slice(explode('/', $file), -1));
    }

}

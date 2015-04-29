<?php

/**
 * 	FileDataWorker
 *
 *  This class handles processing operations for the FileData model.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\FileData;
use MockMaker\Model\ConfigData;

class FileDataWorker
{

    /**
     * Create & populate a new FileData object.
     *
     * @param   $file           string
     * @param   $config         ConfigData
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
     * Get the simple file name from a fully qualified file path.
     *
     * @param   $file   string
     * @return  string
     */
    private function getFileName($file)
    {
        return join('', array_slice(explode('/', $file), -1));
    }

}

<?php

/**
 * 	MockMakerFileWorker
 *
 *  This class handles processing operations for the MockMakerFile model.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerFile;

class MockMakerFileWorker
{

    /**
     * Create & populate a new MockMakerFile object.
     *
     * @param   $file   string
     * @return  MockMakerFile
     */
    public function generateNewObject($file)
    {
        $obj = new MockMakerFile();
        $obj->setFullFilePath($file)
            ->setFileName($this->getFileName($file));

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

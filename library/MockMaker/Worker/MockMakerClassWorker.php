<?php

/**
 * 	MockMakerClassWorker
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\MockMakerFile;
use MockMaker\Model\MockMakerClass;

class MockMakerClassWorker
{

    /**
     * Create a new MockMakerClass object.
     *
     * @param   $fileObj    MockMakerFile
     * @return  MockMakerClass
     */
    public function generateNewObject(MockMakerFile $fileObj)
    {
        return new MockMakerClass();
    }

}

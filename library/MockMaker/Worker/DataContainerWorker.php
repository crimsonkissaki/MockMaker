<?php

/**
 * DataContainerWorker
 *
 * This class handles processing operations for the DataContainer model.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 28, 2015
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\DataContainer;
use MockMaker\Model\ConfigData;

class DataContainerWorker
{

    /**
     * Creates & populates a new DataContainer object
     *
     * @param   string     $file   Fully qualified file path of file to be mocked
     * @param   ConfigData $config ConfigData object
     * @return  DataContainer
     */
    public function generateDataContainerObject($file, ConfigData $config)
    {
        $entityWorker = new EntityDataWorker();
        $mockWorker = new MockDataWorker();

        $obj = new DataContainer();
        $obj->setConfigData($config)
            ->setEntityData($entityWorker->generateEntityDataObject($file, $config))
            ->setMockData($mockWorker->generateMockDataObject($obj->getEntityData(), $config));

        return $obj;
    }
}

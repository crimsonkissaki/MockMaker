<?php

/**
 * DataContainer
 *
 * Holds all file information we need to create the mock file and
 * will be passed to the code generator classes later on
 *
 * @package       MockMaker
 * @author        Evan Johnson
 * @created       Apr 28, 2015
 * @version       1.0
 */

namespace MockMaker\Model;

class DataContainer
{

    /**
     * ConfigData object
     *
     * @var ConfigData
     */
    private $configData;

    /**
     * EntityData object
     *
     * @var EntityData
     */
    private $entityData;

    /**
     * MockData object
     *
     * @var MockData
     */
    private $mockData;

    /**
     * Gets the ConfigData object
     *
     * @return ConfigData
     */
    public function getConfigData()
    {
        return $this->configData;
    }

    /**
     * Gets the target file's EntityData object
     *
     * @return EntityData
     */
    public function getEntityData()
    {
        return $this->entityData;
    }

    /**
     * Gets the MockData object
     *
     * @return MockData
     */
    public function getMockData()
    {
        return $this->mockData;
    }

    /**
     * Sets the ConfigData object
     *
     * @param   ConfigData $configData
     * @return  DataContainer
     */
    public function setConfigData(ConfigData $configData)
    {
        $this->configData = $configData;

        return $this;
    }

    /**
     * Sets the file's EntityData object
     *
     * @param   $entityData  EntityData
     * @return  DataContainer
     */
    public function setEntityData(EntityData $entityData)
    {
        $this->entityData = $entityData;

        return $this;
    }

    /**
     * Sets the MockData object
     *
     * @param   MockData $mockData
     * @return  DataContainer
     */
    public function setMockData(MockData $mockData)
    {
        $this->mockData = $mockData;

        return $this;
    }
}

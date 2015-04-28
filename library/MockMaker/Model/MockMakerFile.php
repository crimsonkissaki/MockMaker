<?php

/**
 * 	MockMakerFile
 *
 *  Holds all file information we need to create the mock file and
 *  will be passed to the code generator classes later on.
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Model;

use MockMaker\Model\MockMakerClass;

class MockMakerFile
{

    /**
     * Fully qualified file path & name
     *
     * @var string
     */
    private $fullFilePath;

    /**
     * Simple file name, e.g. "Customer.php".
     *
     * @var type
     */
    private $fileName;

    /**
     * MockMakerClass data object
     *
     * @var type
     */
    private $mockMakerClass;

    /**
     * Get the file's full path.
     *
     * @return string
     */
    public function getFullFilePath()
    {
        return $this->fullFilePath;
    }

    /**
     * Get the file's name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get the file's MockMakerClass object.
     *
     * @return string
     */
    public function getMockMakerClass()
    {
        return $this->mockMakerClass;
    }

    /**
     * Set the file's full path.
     *
     * @param   $fullFilePath   string
     * @return  MockMakerFile
     */
    public function setFullFilePath($fullFilePath)
    {
        $this->fullFilePath = $fullFilePath;

        return $this;
    }

    /**
     * Set the file's name.
     *
     * @param   $fileName   string
     * @return  MockMakerFile
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Set the file's MockMakerClass object
     *
     * @param   $mockMakerClass  MockMakerClass
     * @return  MockMakerFile
     */
    public function setMockMakerClass(MockMakerClass $mockMakerClass)
    {
        $this->mockMakerClass = $mockMakerClass;

        return $this;
    }

}

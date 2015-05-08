<?php

/**
 * DataContainerWorkerTest
 *
 * @author		Evan Johnson
 * @created     Apr 28, 2015
 * @version     1.0
 */

namespace MockMaker\Worker;

use MockMaker\Model\ConfigData;
use MockMaker\TestHelper\TestHelper;

class DataContainerWorkerTest extends \PHPUnit_Framework_TestCase
{

    // @var $worker DataContainerWorker
    public $worker;
    // @var $config ConfigData
    public $config;
    public $file = '/Applications/XAMPP/xamppfiles/htdocs/mockmaker/tests/MockMaker/Entities/SimpleEntity.php';

    public function setUp()
    {
        $this->worker = new DataContainerWorker();
        $this->config = new ConfigData();
    }

    public function test_generateDataContainerObject_returnsDataContainerObject()
    {
        $this->config->addFilesToMock($this->file);
        $actual = $this->worker->generateDataContainerObject($this->file, $this->config);
        $this->assertInstanceOf('MockMaker\Model\DataContainer', $actual);
    }

}

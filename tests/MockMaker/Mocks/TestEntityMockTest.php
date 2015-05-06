<?php
/**
 * TestEntityMockTest
 *
 * @package:    
 * @author:     johnsone
 * @created:    5/3/15
 */

namespace MockMaker\Mocks;

use MockMaker\Mocks\TestEntityMock;
use MockMaker\Entities\TestEntity;
use MockMaker\TestHelper\TestHelper;

class TestEntityMockTest extends \PHPUnit_Framework_TestCase
{

    /* @var TestEntityMock $mock */
    public $mock;

    public function setUp()
    {
        $this->mock = new TestEntityMock();
    }

    public function _test_getMock_returnsValidMockWithNoArgs()
    {
        $mock = $this->mock->getMock();
        $this->assertInstanceOf('MockMaker\Entities\TestEntity', $mock);
    }

    public function customMockProvider()
    {
        return array(
            array('privateProperty1', 'custom_privateProperty1_value'),
            array('protectedProperty1', 'custom_protectedProperty1_value'),
            array('publicORMSimpleEntityProperty', 'custom_publicORMSimpleEntityProperty_value'),
            array('publicStaticProperty1', 'custom_publicStaticProperty1_value'),
        );
    }

    /**
     * @dataProvider customMockProvider
     */
    public function _test_getMock_returnsValidMockWithCustomArgs($property, $value)
    {
        $args = array( $property => $value );
        $mock = $this->mock->getMock($args);
        $this->assertInstanceOf('MockMaker\Entities\TestEntity', $mock);
    }

    public function test_getMock_usesNullForDefinedCustomArgWithNoValue()
    {
        $args = array( 'publicStaticProperty1' );
        $mock = $this->mock->getMock($args);
        $this->assertInstanceOf('MockMaker\Entities\TestEntity', $mock);
        $this->assertNull($mock::$publicStaticProperty1);
    }

}
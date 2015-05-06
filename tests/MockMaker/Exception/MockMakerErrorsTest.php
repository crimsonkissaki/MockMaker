<?php
/**
 * MockMakerErrorsTest
 *
 * @package     MockMaker
 * @author		Evan Johnson
 * @created	    5/5/15
 * @version     1.0
 */

namespace MockMaker\Exception;

use MockMaker\Exception\MockMakerErrors;

class MockMakerErrorsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_generateMessage_throwsExceptionIfMessageIsNotDefined()
    {
        $msg = 'totallynotreal';
        MockMakerErrors::generateMessage($msg, array('pie'=>'truth'));
    }

    public function test_generateMessage_returnsValidMessage()
    {
        $expected = "Unknown error while attempting to generate mock for class 'TestClass'.";
        $actual = MockMakerErrors::generateMessage(MockMakerErrors::CLASS_CANNOT_BE_MOCKED, array('class'=>'TestClass'));
        $this->assertEquals($expected, $actual);
    }

}
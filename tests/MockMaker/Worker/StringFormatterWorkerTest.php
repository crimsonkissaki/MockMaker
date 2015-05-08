<?php

/**
 * 	StringFormatterWorkerTest
 *
 * 	@author		Evan Johnson
 * 	@created	Apr 28, 2015
 * 	@version	1.0
 */

namespace MockMaker\Worker;

use MockMaker\Worker\StringFormatterWorker as Formatter;

class StringFormatterWorkerTest extends \PHPUnit_Framework_TestCase
{

    public function test_vsprint2_properlyFormatsStringWithSingleArg()
    {
        $string = "This is a test string: %value%";
        $args = array(
            'value' => '_testValue_',
        );
        $expected = "This is a test string: _testValue_";
        $actual = Formatter::vsprintf2($string, $args);
        $this->assertEquals($expected, $actual);
    }

    public function test_vsprint2_properlyFormatsStringWithSingleArg_withDifferentDelimiter()
    {
        $string = "This is a test string: *value*";
        $args = array(
            'value' => '_testValue_',
        );
        $expected = "This is a test string: _testValue_";
        $actual = Formatter::vsprintf2($string, $args, '*');
        $this->assertEquals($expected, $actual);
    }

    public function test_vsprint2_properlyFormatsStringWithMultipleArgs()
    {
        $string = "Test string with multiple values: %foo% %bar% %baz%";
        $args = array(
            'foo' => '_fooValue_',
            'bar' => '_barValue_',
            'baz' => '_bazValue_',
        );
        $expected = "Test string with multiple values: _fooValue_ _barValue_ _bazValue_";
        $actual = Formatter::vsprintf2($string, $args);
        $this->assertEquals($expected, $actual);
    }

    public function test_vsprint2_properlyFormatsStringWithMultipleArgsWithSimilarNames()
    {
        $string = "Test string with multiple values: %foo% %fooBar% %bar% %barBaz% %baz%";
        $args = array(
            'foo' => '_fooValue_',
            'fooBar' => '_fooBarValue_',
            'bar' => '_barValue_',
            'barBaz' => '_barBazValue_',
            'baz' => '_bazValue_',
        );
        $expected = "Test string with multiple values: _fooValue_ _fooBarValue_ _barValue_ _barBazValue_ _bazValue_";
        $actual = Formatter::vsprintf2($string, $args);
        $this->assertEquals($expected, $actual);
    }

}

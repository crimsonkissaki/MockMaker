<?php
/**
 * DebuggerTest
 *
 * @package:    MockMaker
 * @author:     Evan Johnson
 * @created:    5/6/15
 */

namespace MockMaker\Helper;

use MockMaker\Helper\Debugger;

class DebuggerTest extends \PHPUnit_Framework_TestCase
{

    public function test_dbug_returnsValidDebug()
    {
        $var = 'string var';
        ob_start();
        Debugger::dbug($var, 'test var');
        $actual = ob_get_clean();
        $expected = <<<EXPECTED


-------------------------
test var

Var is type: string

string var
-------------------------


EXPECTED;
        $this->assertEquals($expected, $actual);
    }

    public function test_oneLine_returnsValidOneLinerDebug()
    {
        $var = 'string var';
        ob_start();
        Debugger::oneLine($var, 'test var');
        $actual = ob_get_clean();
        $expected = "test var | var type:'string' : value:'string var'\n";
        $this->assertEquals($expected, $actual);
    }

}

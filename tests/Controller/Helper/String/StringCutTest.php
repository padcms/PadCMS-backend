<?php

class StringCutTest extends PHPUnit_Framework_TestCase
{
    public function testShouldCutLongString()
    {
        //GIVEN
        $string = "String with a lot of symbols";
        $helper = new AM_Controller_Action_Helper_String();

        //WHEN
        $return = $helper->cut($string, 6);

        //THEN
        $this->assertEquals('String...', $return);
    }

    public function testShouldNotCutShortString()
    {
        //GIVEN
        $string = "String";
        $helper = new AM_Controller_Action_Helper_String();

        //WHEN
        $return = $helper->cut($string, 6);

        //THEN
        $this->assertEquals('String', $return);
    }
}

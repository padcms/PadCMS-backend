<?php

class HandlerLocatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //GIVEN
        $this->locator = AM_Handler_Locator::getInstance();
    }

    public function testShouldSetHandlerClassName()
    {
        //WHEN
        $this->locator->setHandler('mock', 'Mock_Handler');

        //THEN
        $this->assertTrue($this->locator->getHandler('mock') instanceof Mock_Handler);
    }

    public function testShouldSetHandlerObject()
    {
        //GIVEN
        $handler = new Mock_Handler();
        $handler->property = "value";

        //WHEN
        $this->locator->setHandler('mock', $handler);

        //THEN
        $this->assertTrue($this->locator->getHandler('mock') instanceof Mock_Handler);
        $this->assertEquals("value", $this->locator->getHandler('mock')->property);
    }

    public function testShouldThrowExceptionWhenSetIncorrectHandlerClass()
    {
        //GIVEN
        $this->setExpectedException("AM_Handler_Locator_Exception", "", 502);

        //WHEN
        $this->locator->setHandler('mock', 'Mock_Wrong_Handler');
    }

    public function testShouldThrowExceptionWhenSetIncorrectHandlerObject()
    {
        //GIVEN
        $handler = new Mock_Wrong_Handler();
        $this->setExpectedException("AM_Handler_Locator_Exception", "", 502);

        //WHEN
        $this->locator->setHandler('mock', 'Mock_Wrong_Handler');
    }

    public function testShouldThrowExceptionWhenTryingToGetUnsetedHandler()
    {
        //GIVEN
        $this->setExpectedException("AM_Handler_Locator_Exception", "", 501);

        //WHEN
        $this->locator->getHandler('false');
    }
}

class Mock_Handler extends AM_Handler_Abstract
{
    public $property = null;
}

class Mock_Wrong_Handler
{}
<?php

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_standardMock = $this->getMock('AM_Tools_Standard', array('is_file'));
    }

    public function testShouldCreateSourceInstance()
    {
        //GIVEN
        $sourceFile = 'source_file.mock';

        //WHEN
        $this->_standardMock->expects($this->any())
             ->method('is_file')
             ->with($this->equalTo($sourceFile))
             ->will($this->returnValue(true));

        $source = AM_Resource_Factory::create($sourceFile);

        $this->assertTrue($source instanceof AM_Resource_Concrete_Mock);
    }

    public function testShouldThrowExceptionWhenFileNotFound()
    {
        //GIVEN
        $sourceFile = "source_file.mock";

        //WHEN
        $this->_standardMock->expects($this->any())
             ->method('is_file')
             ->with($this->equalTo($sourceFile))
             ->will($this->returnValue(false));

        //THEN
        $this->setExpectedException('AM_Resource_Factory_Exception', '', 501);

        //WHEN
        $source = AM_Resource_Factory::create($sourceFile);
    }

    public function testShouldThrowExceptionWhenSourceClassNotFound()
    {
        //GIVEN
        $sourceFile = "source_file.fail";

        //WHEN
        $this->_standardMock->expects($this->any())
             ->method('is_file')
             ->with($this->equalTo($sourceFile))
             ->will($this->returnValue(true));

        //THEN
        $this->setExpectedException('AM_Resource_Factory_Exception', '', 502);

        //WHEN
        $source = AM_Resource_Factory::create($sourceFile);
    }
}
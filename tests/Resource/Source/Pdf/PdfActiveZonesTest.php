<?php

class PdfActiveZonesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_pdfFile = dirname(__FILE__)
                . DIRECTORY_SEPARATOR . 'files'
                .DIRECTORY_SEPARATOR . 'file.pdf';
    }

    public function testShouldCreateSourceInstance()
    {
        //GIVEN
        $standardMock = $this->getMock('AM_Tools_Standard', array('file_exists'));
        $source = AM_Resource_Factory::create($this->_pdfFile);
        /* @var $source AM_Image_Source_Pdf */
        $standardMock->expects($this->any())
             ->method('file_exists')
             ->will($this->returnValue(true));

        //WHEN
        $info = $source->getPdfInfo();

        //THEN
        $this->assertEquals(1024, $info['height']);
        $this->assertEquals(768, $info['width']);

        $expectedZone = array('uri' => 'local://navigation/100_tweets',
                              'llx' => '508.0',
                              'lly' => '614.604',
                              'urx' => '734.5',
                              'ury' => '388.104'
        );

        $this->assertEquals($expectedZone, $info['zones'][0]);
    }
}
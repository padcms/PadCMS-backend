<?php
class StaticPdfResourceCopyTest extends PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject $standardMock **/
    var $standardMock = null;

    public function setUp()
    {
        $this->pdf = new AM_Model_Db_StaticPdf();

        $this->pdf->setFromArray(array("id"     => 1,
                                       "issue"  => 1,
                                       "name"   => "resource.pdfmock"
                                     ));

        $this->standardMock = $this->getMock("AM_Tools_Standard", array("is_dir", "mkdir", "copy"));
    }

    public function testShouldCopyResource()
    {
        //GIVEN
        $resource = new AM_Model_Db_StaticPdf_Data_Resource($this->pdf);
        $this->pdf->id    = 2;
        $this->pdf->issue = 2;

        //THEN
        $oldDir = AM_Tools::getContentPath(AM_Model_Db_StaticPdf_Data_Resource::TYPE, 1);
        $newDir = AM_Tools::getContentPath(AM_Model_Db_StaticPdf_Data_Resource::TYPE, 2);

        $this->standardMock->expects($this->at(0))
             ->method("is_dir")
             ->with($this->equalTo($oldDir))
             ->will($this->returnValue(true));

        $this->standardMock->expects($this->at(1))
             ->method("is_dir")
             ->with($this->equalTo($newDir))
             ->will($this->returnValue(false));

        $this->standardMock->expects($this->once())
             ->method("mkdir")
             ->with($this->equalTo($newDir),  $this->equalTo(0777), $this->equalTo(true))
             ->will($this->returnValue(true));

        $this->standardMock->expects($this->at(3))
             ->method("copy")
             ->with($this->equalTo($oldDir . DIRECTORY_SEPARATOR . "1.pdfmock"),
                    $this->equalTo($newDir . DIRECTORY_SEPARATOR . "2.pdfmock"))
             ->will($this->returnValue(true));

        //WHEN
        $resource->copy();
    }
}

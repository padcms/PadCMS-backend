<?php
class TermResourceCopyTest extends PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject $standardMock **/
    var $standardMock = null;

    public function setUp()
    {
        $this->term = new AM_Model_Db_Term();

        $this->term->setFromArray(array("id" => 1,
                                        "thumb_stripe"  => "resource_thumb_stripe.ext",
                                        "thumb_summary" => "resource_thumb_summary.ext"
                                     ));

        $this->standardMock = $this->getMock("AM_Tools_Standard", array("is_dir", "mkdir", "copy"));
    }

    public function testShouldCopyResource()
    {
        //GIVEN
        $resource = new AM_Model_Db_Term_Data_Resource($this->term);
        $this->term->id = 2;

        //THEN
        $oldDir = AM_Tools::getContentPath(AM_Model_Db_Term_Data_Resource::TYPE, 1);
        $newDir = AM_Tools::getContentPath(AM_Model_Db_Term_Data_Resource::TYPE, 2);

        $this->standardMock->expects($this->at(0))
             ->method("is_dir")
             ->with($this->equalTo($oldDir))
             ->will($this->returnValue(true));

        //thumb_summary
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
             ->with($this->equalTo($oldDir . DIRECTORY_SEPARATOR . "summary.ext"),
                    $this->equalTo($newDir . DIRECTORY_SEPARATOR . "summary.ext"))
             ->will($this->returnValue(true));

        //thumb_summary
        $this->standardMock->expects($this->at(4))
             ->method("is_dir")
             ->with($this->equalTo($newDir))
             ->will($this->returnValue(true));

        $this->standardMock->expects($this->at(5))
             ->method("copy")
             ->with($this->equalTo($oldDir . DIRECTORY_SEPARATOR . "stripe.ext"),
                    $this->equalTo($newDir . DIRECTORY_SEPARATOR . "stripe.ext"))
             ->will($this->returnValue(true));

        //WHEN
        $resource->copy();
    }
}

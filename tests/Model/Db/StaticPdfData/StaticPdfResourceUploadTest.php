<?php
class StaticPdfResourceUploadTest extends AM_Test_PHPUnit_DatabaseTestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject **/
    protected $_standardMock = null;
    /** @var PHPUnit_Framework_MockObject_MockObject **/
    protected $_uploaderMock = null;

    public function getDataSet()
    {
        $tableNames = array('static_pdf');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $this->pdf = new AM_Model_Db_StaticPdf();

        $this->pdf->setFromArray(array('id'     => 1,
                                       'issue'  => 1,
                                       'name'   => ''
                                     ));
        $this->pdf->save();

        $this->_standardMock = $this->getMock('AM_Tools_Standard', array('is_dir', 'mkdir'));
        $this->_uploaderMock = $this->getMock('AM_Handler_Upload', array('isValid', 'isUploaded', 'getFileInfo', 'receive'));
    }

    public function testShouldUploadResource()
    {
        //GIVEN
        $thumbnailerMock = $this->getMock('AM_Handler_Thumbnail', array('addSourceFile', 'loadAllPresets', 'createThumbnails', 'getSources'));
        AM_Handler_Locator::getInstance()->setHandler('thumbnail', $thumbnailerMock);

        $resource    = new AM_Model_Db_StaticPdf_Data_Resource($this->pdf);
        $resource->setUploader($this->_uploaderMock);
        $resourceDir = $resource->getResourceDir();

        //THEN
        $this->_standardMock->expects($this->once())
             ->method('is_dir')
             ->with($this->equalTo($resourceDir))
             ->will($this->returnValue(false));

        $this->_standardMock->expects($this->once())
             ->method('mkdir')
             ->with($this->equalTo($resourceDir),  $this->equalTo(0777), $this->equalTo(true))
             ->will($this->returnValue(true));

        $this->_uploaderMock->expects($this->once())
             ->method('isUploaded')
             ->will($this->returnValue(true));

        $this->_uploaderMock->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->_uploaderMock->expects($this->once())
             ->method('getFileInfo')
             ->will($this->returnValue(array('pdf-file'=>array('name'=>'test.pdf'))));

        $this->_uploaderMock->expects($this->once())
             ->method('receive')
             ->will($this->returnValue(true));

        $thumbnailerMock->expects($this->any())
                ->method('addSourceFile')
                ->will($this->returnValue($thumbnailerMock));

        $thumbnailerMock->expects($this->any())
                ->method('loadAllPresets')
                ->will($this->returnValue($thumbnailerMock));

        $thumbnailerMock->expects($this->any())
                ->method('getSources')
                ->will($this->returnValue(array()));

        //WHEN
        $resource->upload();

        //THEN
        $this->assertEquals('test.pdf', $resource->getResourceDbBaseName());
    }
}

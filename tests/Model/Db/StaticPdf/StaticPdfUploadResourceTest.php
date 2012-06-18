<?php

class StaticPdfUploadResourceTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject **/
    protected $_resourceMock = null;
    /** @var AM_Model_Db_StaticPdf **/
    protected $_staticPdf    = null;

    public function getDataSet()
    {
        $tableNames = array('static_pdf');
        $dataSet = $this->getConnection()->createDataSet($tableNames);

        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $staticPdfData = array("id" => 1, "issue" => 1, "name" => "");
        $this->_staticPdf = new AM_Model_Db_StaticPdf();
        $this->_staticPdf->setFromArray($staticPdfData);
        $this->_staticPdf->save();

        $this->_resourceMock = $this->getMock('AM_Model_Db_StaticPdf_Data_Resource', array('upload', 'getResourceDbBaseName'), array($this->_staticPdf));
        $this->_staticPdf->setResources($this->_resourceMock);
    }

    public function testShouldUploadResource()
    {
        //GIVEN
        $this->_resourceMock->expects($this->once())
             ->method('upload');

        $this->_resourceMock->expects($this->once())
             ->method('getResourceDbBaseName')
             ->will($this->returnValue('static.pdf'));

        //WHEN
        $this->_staticPdf->uploadResource();
        $this->_staticPdf->refresh();

        //THEN
        $queryTable    = $this->getConnection()->createQueryTable("static_pdf", "SELECT id, issue, name FROM static_pdf ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/upload.xml")
                              ->getTable("static_pdf");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

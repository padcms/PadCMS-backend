<?php

class StaticPdfCopyToIssueTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('static_pdf');
        $dataSet = $this->getConnection()->createDataSet($tableNames);

        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $staticPdfData = array("id" => 1, "issue" => 1, "name" => "static.pdf");
        $this->staticPdf = new AM_Model_Db_StaticPdf();
        $this->staticPdf->setFromArray($staticPdfData);
        $this->staticPdf->save();

        $this->resourceMock = $this->getMock('AM_Model_Db_StaticPdf_Data_Resource', array('copy'), array($this->staticPdf));
        $this->staticPdf->setResources($this->resourceMock);
    }

    public function testShouldCopyToIssue()
    {
        //GIVEN
        $issueData = array("id" => 2, "title" => "test_page");
        $issue = new AM_Model_Db_Issue();
        $issue->setFromArray($issueData);

        //THEN
        $this->resourceMock->expects($this->once())
            ->method('copy');

        //WHEN
        $this->staticPdf->copyToIssue($issue);
        $this->staticPdf->refresh();

        //THEN
        $this->assertEquals(2, $this->staticPdf->issue, "Issue id should change");

        $queryTable    = $this->getConnection()->createQueryTable("static_pdf", "SELECT id, issue FROM static_pdf ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy.xml")
                              ->getTable("static_pdf");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

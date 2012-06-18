<?php

class IssueCopyToUserTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('issue');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $issueData = array("id" => 1, "title" => "test_issue", "state" => 1, "application" => 1, "user" => 1);
        $this->issue = new AM_Model_Db_Issue();
        $this->issue->setFromArray($issueData);
        $this->issue->save();

        $this->revisionSetMock = $this->getMock('AM_Model_Db_Rowset_Revision', array('copyToIssue'), array(array("readOnly" => true)));
        $this->issue->setRevisions($this->revisionSetMock);

        $this->horizontalPdfSetMock = $this->getMock('AM_Model_Db_Rowset_StaticPdf', array('copyToIssue'), array(array("readOnly" => true)));
        $this->issue->setHorizontalPdfs($this->horizontalPdfSetMock);
    }

    public function testShouldCopyToUser()
    {
        //GIVEN
        $userData = array("id" => 2, "login" => "test_user", "client" => 2);
        $user = new AM_Model_Db_User(array("data" => $userData));

        //THEN
        $this->revisionSetMock->expects($this->once())
            ->method('copyToIssue');
        $this->horizontalPdfSetMock->expects($this->once())
            ->method('copyToIssue');

        //WHEN
        $this->issue->copyToUser($user);
        $this->issue->refresh();

        //THEN
        $this->assertEquals(2, $this->issue->user, "User id should change");

        $queryTable    = $this->getConnection()->createQueryTable("issue", "SELECT id, user, application FROM issue ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy2user.xml")
                              ->getTable("issue");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

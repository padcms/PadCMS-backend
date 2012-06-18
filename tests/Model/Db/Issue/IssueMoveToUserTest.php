<?php

class IssueMoveToUserTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('issue');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp(){
        parent::setUp();

        $issueData = array("id" => 1, "title" => "test_issue", "application" => 1, "user" => 1);
        $this->issue = new AM_Model_Db_Issue();
        $this->issue->setFromArray($issueData);
        $this->issue->save();

        $this->revisionSetMock = $this->getMock('AM_Model_Db_Rowset_Revision', array('moveToIssue'), array(array("readOnly" => true)));
        $this->issue->setRevisions($this->revisionSetMock);
    }

    public function testShouldCopyToUser()
    {
        //GIVEN
        $userData = array("id" => 2, "login" => "test_user", "client" => 2);
        $user = new AM_Model_Db_User(array("data" => $userData));

        //THEN
        $this->revisionSetMock->expects($this->once())
            ->method('moveToIssue');

        //WHEN
        $this->issue->moveToUser($user);
        $this->issue->refresh();

        //THEN
        $this->assertEquals(2, $this->issue->user, "User id should change");

        $queryTable    = $this->getConnection()->createQueryTable("issue", "SELECT id, user, application FROM issue ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/move2user.xml")
                              ->getTable("issue");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testShouldNotCopyToSameUser()
    {
        //GIVEN
        $userData = array("id" => 1, "login" => "test_user", "client" => 1);
        $user = new AM_Model_Db_User(array("data" => $userData));

        //THEN
        $this->revisionSetMock->expects($this->never())
            ->method('moveToIssue');

        //WHEN
        $this->issue->moveToUser($user);
        $this->issue->refresh();

        //THEN
        $this->assertEquals(1, $this->issue->user, "User id should not change");

        $queryTable    = $this->getConnection()->createQueryTable("issue", "SELECT id, user, application FROM issue ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/not_move2user.xml")
                              ->getTable("issue");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

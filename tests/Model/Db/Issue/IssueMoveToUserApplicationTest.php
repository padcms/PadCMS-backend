<?php

class IssueMoveToUserApplicationTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function setUp(){
        parent::setUp();

        $issueData = array("id" => 1, "title" => "test_issue", "state" => 1, "application" => 1, "user" => 1);
        $this->issue = new AM_Model_Db_Issue();
        $this->issue->setFromArray($issueData);
        $this->issue->save();

        $this->revisionSetMock = $this->getMock('AM_Model_Db_Rowset_Revision', array('moveToIssue'), array(array("readOnly" => true)));
        $this->issue->setRevisions($this->revisionSetMock);
    }

    public function getDataSet()
    {
        $tableNames = array('issue');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function testShouldMoveToUserApplication()
    {
        //GIVEN
        $userData = array("id" => 2, "login" => "test_user", "client" => 2);
        $user     = new AM_Model_Db_User(array("data" => $userData));

        $appData = array("id" => 2, "title" => "test_app", "client" => 2);
        $app     = new AM_Model_Db_Application(array("data" => $appData));

        //THEN
        $this->revisionSetMock->expects($this->once())
            ->method('moveToIssue');

        //WHEN
        $this->issue->moveToUserApplication($user, $app);
        $this->issue->refresh();

        //THEN
        $this->assertEquals(2, $this->issue->user, "User id should change");
        $this->assertEquals(2, $this->issue->application, "Application id should change");

        $queryTable    = $this->getConnection()->createQueryTable("issue", "SELECT id, user, application FROM issue ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/move2userapp.xml")
                              ->getTable("issue");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testShouldNotMoveToSameUserAndSameApplication()
    {
        //GIVEN
        $userData = array("id" => 1, "login" => "test_user", "client" => 1);
        $user     = new AM_Model_Db_User(array("data" => $userData));

        $appData = array("id" => 1, "title" => "test_app", "client" => 1);
        $app     = new AM_Model_Db_Application(array("data" => $appData));

        //THEN
        $this->revisionSetMock->expects($this->never())
            ->method('moveToIssue');

        //WHEN
        $this->issue->moveToUserApplication($user, $app);
        $this->issue->refresh();

        //THEN
        $this->assertEquals(1, $this->issue->user, "User id should not change");
        $this->assertEquals(1, $this->issue->application, "Application id should not change");

        $queryTable    = $this->getConnection()->createQueryTable("issue", "SELECT id, user, application FROM issue ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/not_move2userapp.xml")
                              ->getTable("issue");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

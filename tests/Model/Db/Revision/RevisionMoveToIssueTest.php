<?php

class RevisionMoveToIssueTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('revision');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $revisionData = array("id" => 1, "title" => "test_revision", "state" => 1, "issue" => 1, "user" => 1);
        $this->revision = new AM_Model_Db_Revision();
        $this->revision->setFromArray($revisionData);
        $this->revision->save();

        $appData = array("id" => 1, "title" => "test_app1", "client" => 1);
        $this->app     = new AM_Model_Db_Application(array("data" => $appData));

        $issueData = array("id" => 1, "title" => "test_issue1", "user" => 1);
        $issue     = new AM_Model_Db_Issue(array("data" => $issueData));
        $this->revision->setApplication($this->app);
        $this->revision->setIssue($issue);

        $this->pageMock = $this->getMock('AM_Model_Db_Page', array('moveToRevision'), array(), '', false);
        $this->revision->setPages(array($this->pageMock));
    }

    public function testShouldMoveToIssue()
    {
        //GIVEN
        $appData = array("id" => 2, "title" => "test_app2", "client" => 2);
        $app     = new AM_Model_Db_Application(array("data" => $appData));

        $issueData = array("id" => 2, "title" => "test_issue", "user" => 2);
        $issue = new AM_Model_Db_Issue(array("data" => $issueData));
        $issue->setApplication($app);

        //THEN
        $this->pageMock->expects($this->once())
                        ->method('moveToRevision');

        //WHEN
        $this->revision->moveToIssue($issue);
        $this->revision->refresh();

        //THEN
        $this->assertEquals(2, $this->revision->issue, "Issue id should change");
        $this->assertEquals(2, $this->revision->user, "User id should change");

        $queryTable    = $this->getConnection()->createQueryTable("revision", "SELECT id, user, issue FROM revision ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/move2issue.xml")
                              ->getTable("revision");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testShouldNotMoveToSameIssue()
    {
        //GIVEN
        $issueData = array("id" => 1, "title" => "test_issue1", "user" => 1, "application" => 1);
        $issue = new AM_Model_Db_Issue(array("data" => $issueData));
        $issue->setApplication($this->app);

        //THEN
        $this->pageMock->expects($this->never())
                        ->method('moveToRevision');

        //WHEN
        $this->revision->moveToIssue($issue);
        $this->revision->refresh();

        //THEN
        $this->assertEquals(1, $this->revision->issue, "Issue id should not change");
        $this->assertEquals(1, $this->revision->user, "User id should not change");

        $queryTable    = $this->getConnection()->createQueryTable("revision", "SELECT id, user, issue FROM revision ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/not_move2issue.xml")
                              ->getTable("revision");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

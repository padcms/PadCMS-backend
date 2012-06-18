<?php

class RevisionCopyToIssueTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('revision', 'application', 'issue', 'term', 'page_background', 'page', 'element');
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
        $app     = new AM_Model_Db_Application();
        $app->setFromArray($appData);
        $app->save();

        $issueData = array("id" => 1, "title" => "test_issue1", "application" => 1, "user" => 1);
        $issue     = new AM_Model_Db_Issue();
        $issue->setFromArray($issueData);
        $issue->save();
        $this->revision->setApplication($app);
        $this->revision->setIssue($issue);
    }

    public function testShouldCopyToIssue()
    {
        //GIVEN
        $appData = array("id" => 2, "title" => "test_app", "client" => 2);
        $app     = new AM_Model_Db_Application(array("data" => $appData));

        $issueData = array("id" => 2, "title" => "test_user", "user" => 2);
        $issue = new AM_Model_Db_Issue(array("data" => $issueData));
        $issue->setApplication($app);

        //WHEN
        $this->revision->copyToIssue($issue);
        $this->revision->refresh();

        //THEN
        $this->assertEquals(2, $this->revision->issue, "Issue id should change");
        $this->assertEquals(2, $this->revision->user, "User id should change");

        $queryTable    = $this->getConnection()->createQueryTable("revision", "SELECT id, user, issue FROM revision ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy2issue.xml")
                              ->getTable("revision");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

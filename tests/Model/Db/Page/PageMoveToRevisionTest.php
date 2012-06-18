<?php

class PageMoveToRevisionTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('page');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $pageData = array("id" => 1, "title" => "test_page", "revision" => 1, "user" => 1);
        $this->page = new AM_Model_Db_Page();
        $this->page->setFromArray($pageData);
        $this->page->save();
    }

    public function testShouldMoveToRevision()
    {
        //GIVEN
        $revisionData = array("id" => 2, "title" => "test_revision2", "state" => 1, "issue" => 2, "user" => 2);
        $revisionTo = new AM_Model_Db_Revision();
        $revisionTo->setFromArray($revisionData);

        //WHEN
        $this->page->moveToRevision($revisionTo);

        //THEN
        $this->assertEquals(2, $this->page->user, "User id should change");
        $this->assertEquals(2, $this->page->revision, "Revision id should change");

        $queryTable    = $this->getConnection()->createQueryTable("page", "SELECT id, user, revision FROM page ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/move2revision.xml")
                              ->getTable("page");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

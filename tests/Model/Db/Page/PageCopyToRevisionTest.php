<?php

class PageCopyToRevisionTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'PageCopyToRevisionTest.yml';
    }

    public function setUp()
    {
        parent::setUp();

        $this->page = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => 1));

        $this->elementsMock       = $this->getMock('AM_Model_Db_Rowset_Element', array('copyToPage'), array(array("readOnly" => true)));
        $this->pageBackgroundMock = $this->getMock('AM_Model_Db_PageBackground', array('copyToPage'), array(array("readOnly" => true, "table" => new AM_Model_Db_Table_PageBackground())));
        $this->termMock           = $this->getMock('AM_Model_Db_Term', array('saveToPage'), array(array("readOnly" => true, "table" => new AM_Model_Db_Table_PageBackground())));

        $this->page->setElements($this->elementsMock);
        $this->page->setPageBackground($this->pageBackgroundMock);
        $this->page->addTerm($this->termMock);
    }

    public function testShouldCopyToRevision()
    {
        //GIVEN
        $revisionData = array("id" => 2, "title" => "test_revision2", "state" => 1, "issue" => 2, "user" => 2);
        $revisionTo = new AM_Model_Db_Revision();
        $revisionTo->setFromArray($revisionData);
        $revisionTo->save();

        //THEN
        $this->elementsMock->expects($this->once())
            ->method('copyToPage');
        $this->pageBackgroundMock->expects($this->once())
            ->method('copyToPage');
        $this->termMock->expects($this->once())
            ->method('saveToPage');

        //WHEN
        $this->page->copyToRevision($revisionTo);

        //THEN
        $this->assertEquals(2, $this->page->user, "User id should change");
        $this->assertEquals(2, $this->page->revision, "Revision id should change");

        $queryTable    = $this->getConnection()->createQueryTable("page", "SELECT id, user, revision FROM page ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy2revision.xml")
                              ->getTable("page");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

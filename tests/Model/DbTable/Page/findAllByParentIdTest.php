<?php

class findAllByParentIdTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('page', 'page_imposition');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();
        //GIVEN
        $pageLeftData = array("id" => 1, "title" => "Left page", "revision" => 1, "user" => 1);
        $this->pageLeft = new AM_Model_Db_Page();
        $this->pageLeft->setFromArray($pageLeftData);
        $this->pageLeft->save();

        $pageRootData = array("id" => 2, "title" => "Root page", "revision" => 1, "user" => 1);
        $this->pageRoot = new AM_Model_Db_Page();
        $this->pageRoot->setFromArray($pageRootData);
        $this->pageRoot->save();


        $pageRightData = array("id" => 3, "title" => "Right page", "revision" => 1, "user" => 1);
        $this->pageRight = new AM_Model_Db_Page();
        $this->pageRight->setFromArray($pageRightData);
        $this->pageRight->save();

        $tablePageImposition = new AM_Model_Db_Table_PageImposition();
        $data = array("page"         => $this->pageLeft->id,
                      "is_linked_to" => $this->pageRoot->id,
                      "link_type"    => AM_Model_Db_Page::LINK_RIGHT);
        $tablePageImposition->insert($data);
        $data = array("page"         => $this->pageRoot->id,
                      "is_linked_to" => $this->pageRight->id,
                      "link_type"    => AM_Model_Db_Page::LINK_RIGHT);
        $tablePageImposition->insert($data);
    }

    public function testShouldCopyToRevision()
    {
        //WHEN
        $rows = AM_Model_Db_Table_Abstract::factory('page')->findAllByParentId($this->pageRoot->id);

        //THEN
        $this->assertEquals(2, $rows->count(), 'Rowset must contain two elements');

        $right = $rows[0];
        $this->assertEquals($this->pageRight->id, $right->id, 'Right page has wrong id');
        $this->assertEquals(AM_Model_Db_Page::LINK_RIGHT, $right->getLinkType(), 'Right page has wrong link type');

        $left = $rows[1];
        $this->assertEquals($this->pageLeft->id, $left->id, 'Left page has wrong id');
        $this->assertEquals(NULL, $left->getLinkType(), 'Left page has wrong link type');
    }
}

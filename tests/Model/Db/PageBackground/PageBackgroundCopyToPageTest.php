<?php

class PageBackgroundCopyToPageTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('page_background');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $backgroundData = array("id" => 1, "page" => 1, "type" => "element");
        $this->background = new AM_Model_Db_PageBackground();
        $this->background->setFromArray($backgroundData);
        $this->background->save();

        $elementData = array("id" => 1, "field" => 1, "page" => 1);
        $element = new AM_Model_Db_Element();
        $element->setFromArray($elementData);

        $this->background->setElement($element);
    }

    public function testShouldCopyToPage()
    {
        //GIVEN
        $pageData = array("id" => 2, "title" => "test_page");
        $page = new AM_Model_Db_Page();
        $page->setFromArray($pageData);


        //WHEN
        $elementData = array("id" => 2, "field" => 1, "page" => 2);
        $element = new AM_Model_Db_Element();
        $element->setFromArray($elementData);
        $this->background->setElement($element);
        $this->background->copyToPage($page);

        //THEN
        $this->assertEquals(2, $this->background->page, "Page id should change");

        $queryTable    = $this->getConnection()->createQueryTable("page_background", "SELECT id, page FROM page_background ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy2page.xml")
                              ->getTable("page_background");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

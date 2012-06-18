<?php

class ElementCopyToPageTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'copy_to_page.yml';
    }

    public function testShouldCopyToPage()
    {
        //GIVEN
        $this->element = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', 1);
        $this->resourceMock = $this->getMock('AM_Model_Db_Element_Data_Resource', array('copy'), array($this->element));
        $this->element->setResources($this->resourceMock);

        $pageData = array("id" => 2, "title" => "test_page");
        $page = new AM_Model_Db_Page();
        $page->setFromArray($pageData);

        //THEN
        $this->resourceMock->expects($this->once())
            ->method('copy');

        //WHEN
        $this->element->copyToPage($page);
        $this->element->refresh();

        //THEN
        $this->assertEquals(2, $this->element->page, "Page id should change");

        $queryTable    = $this->getConnection()->createQueryTable("element", "SELECT id, page FROM element ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy.xml")
                              ->getTable("element");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

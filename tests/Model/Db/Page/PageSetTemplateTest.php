<?php

class PageSetTemplateTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'set_template.yml';
    }

    public function testShouldSetTemplate()
    {
        //GIVEN
        $page        = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => 1));
        /* @var $page AM_Model_Db_Page */
        $newTemplate = AM_Model_Db_Table_Abstract::factory('template')->findOneBy(array('id' => 2));
        /* @var $newTemplate AM_Model_Db_Template */

        //WHEN
        $page->setTemplate($newTemplate);

        //THEN
        //Checking page table
        $queryTable    = $this->getConnection()->createQueryTable('page', 'SELECT id, template FROM page ORDER BY id');
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/setTemplate.xml')
                              ->getTable('page');
        $this->assertTablesEqual($expectedTable, $queryTable);

        //Checking element table
        $queryTable    = $this->getConnection()->createQueryTable('element', 'SELECT id, page, field FROM element ORDER BY id');
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/setTemplate.xml')
                              ->getTable('element');
        $this->assertTablesEqual($expectedTable, $queryTable);

        //Check page background
        $this->assertEquals(0, AM_Model_Db_Table_Abstract::factory('page_background')->fetchAll()->count(), 'Error while assering, that "page_background" table is empty');
    }
}

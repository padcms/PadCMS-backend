<?php

class HandlerPageAddBetweenTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'HandlerPageAddBetweenTest.yml';
    }

    public function testAddPage()
    {
        //GIVEN
        $oPage     = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => 2));
        $oTemplate = AM_Model_Db_Table_Abstract::factory('template')->findOneBy(array('id' => 1));
        $aUser     = array('id' => 1);

        $oHandler = new AM_Handler_Page();

        //WHEN
        $oNewPage = $oHandler->addPage($oPage, $oTemplate, AM_Model_Db_Page::LINK_RIGHT, $aUser, true);

        //THEN
        $oGivenDataSet    = $this->getConnection()->createQueryTable('page', 'SELECT id, title, revision, user, template, connections FROM page ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/HandlerPageAddBetweenTest.xml')
                              ->getTable('page');
        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);

        $oGivenDataSet    = $this->getConnection()->createQueryTable('page_imposition', 'SELECT page, is_linked_to, link_type FROM page_imposition ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/HandlerPageAddBetweenTest.xml')
                              ->getTable('page_imposition');
        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }


}
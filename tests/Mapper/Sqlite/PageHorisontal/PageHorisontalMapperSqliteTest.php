<?php
/**
 * @author vl4dimir
 */
class PageHorisontalMapperSqliteTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var AM_Model_Db_Page */
    private $_page = null;
    /** @var Zend_Db_Adapter_Abstract **/
    protected $_oAdapter = null;
    /** @var Zend_Test_PHPUnit_Db_Connection **/
    protected $_oSqliteConnectionMock = null;

    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'PageHorisontalMapperSqliteTest.yml';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->_prepareSqliteAdapter();
    }


    protected function _prepareSqliteAdapter()
    {
        $sDbName = AM_Handler_Temp::getInstance()->getFile();
        $this->_oAdapter = Zend_Db::factory('PDO_SQLITE', array('dbname' => $sDbName));
        $sSql = <<<SQL
CREATE TABLE `page_horisontal`
(
       id INTEGER NOT NULL,
       name TEXT,
       resource TEXT,
       PRIMARY KEY(id)
);
SQL;
        $this->_oAdapter->getConnection()->exec($sSql);

        $this->_oSqliteConnectionMock = $this->createZendDbConnection($this->_oAdapter, $sDbName);

        return $this;
    }

    public function testShouldUnmapPage()
    {
        //GIVEN
        $oPage = AM_Model_Db_Table_Abstract::factory('page_horisontal')->findOneBy('id', 1);

        $oMapper = AM_Mapper_Abstract::factory($oPage, "sqlite", array('adapter' => $this->_oAdapter));
        /* @var $oMapper AM_Mapper_Sqlite_Page */

        //WHEN
        $oMapper->unmap();

        //THEN
        $oGivenDataSet    = $this->_oSqliteConnectionMock->createQueryTable("page_horisontal", "SELECT id, resource FROM page_horisontal ORDER BY id");
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/PageHorisontalMapperSqliteTest.xml')
                              ->getTable('page_horisontal');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
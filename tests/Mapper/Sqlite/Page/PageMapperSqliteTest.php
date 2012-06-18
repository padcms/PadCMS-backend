<?php
/**
 * @author vl4dimir
 */
class PageMapperSqliteTest extends AM_Test_PHPUnit_DatabaseTestCase
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
                . DIRECTORY_SEPARATOR . 'PageMapperSqliteTest.yml';
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
CREATE TABLE `page`
(
       id INTEGER NOT NULL,
       title TEXT NOT NULL,
       horisontal_page_id INTEGER NOT NULL DEFAULT "-1",
       template INTEGER NOT NULL DEFAULT "-1" ,
       machine_name TEXT,
       PRIMARY KEY(id)
)
SQL;
        $this->_oAdapter->getConnection()->exec($sSql);

        $this->_oSqliteConnectionMock = $this->createZendDbConnection($this->_oAdapter, $sDbName);

        return $this;
    }

    public function testShouldUnmapPage()
    {
        //GIVEN
        $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', 1);

        $oMapper = AM_Mapper_Abstract::factory($oPage, "sqlite", array('adapter' => $this->_oAdapter));
        /* @var $oMapper AM_Mapper_Sqlite_Page */

        //WHEN
        $oMapper->unmap();

        //THEN
        $oGivenDataSet    = $this->_oSqliteConnectionMock->createQueryTable("page", "SELECT id, title, horisontal_page_id, template, machine_name FROM page ORDER BY id");
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/PageMapperSqliteTest.xml')
                              ->getTable('page');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
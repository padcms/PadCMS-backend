<?php
/**
 * @author vl4dimir
 */
class TermMapperSqliteTest extends AM_Test_PHPUnit_DatabaseTestCase
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
                . DIRECTORY_SEPARATOR . 'TermMapperSqliteTest.yml';
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
CREATE TABLE `menu`
(
       id INTEGER NOT NULL,
       title TEXT,
       firstpage_id INTEGER,
       description TEXT,
       thumb_stripe TEXT,
       thumb_summary TEXT,
       color TEXT,
       PRIMARY KEY(id)
);
SQL;
        $this->_oAdapter->getConnection()->exec($sSql);

        $this->_oSqliteConnectionMock = $this->createZendDbConnection($this->_oAdapter, $sDbName);

        return $this;
    }

    public function testShouldUnmapTerm()
    {
        //GIVEN
        $oPage = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', 1);

        $oMapper = AM_Mapper_Abstract::factory($oPage, "sqlite", array('adapter' => $this->_oAdapter));
        /* @var $oMapper AM_Mapper_Sqlite_Page */

        //WHEN
        $oMapper->unmap();

        //THEN
        $oGivenDataSet    = $this->_oSqliteConnectionMock->createQueryTable("menu", "SELECT title, description, thumb_stripe, thumb_summary, color, firstpage_id FROM menu ORDER BY id");
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/TermMapperSqliteTest.xml')
                              ->getTable('menu');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
<?php
/**
 * @author vl4dimir
 */
class PageImpositionMapperSqliteTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var AM_Model_Db_Page */
    private $_page = null;
    /** @var Zend_Db_Adapter_Abstract **/
    protected $_oAdapter = null;
    /** @var Zend_Test_PHPUnit_Db_Connection **/
    protected $_oConnectionMock = null;

    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'PageImpositionMapperSqliteTest.yml';
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
CREATE TABLE `page_imposition`
(
       id INTEGER NOT NULL,
       page_id INTEGER NOT NULL,
       is_linked_to INTEGER NOT NULL,
       position_type TEXT NOT NULL,
       PRIMARY KEY(id)
);
SQL;
        $this->_oAdapter->getConnection()->exec($sSql);

        $this->_oConnectionMock = $this->createZendDbConnection($this->_oAdapter, $sDbName);

        return $this;
    }

    public function testShouldUnmapPageImposition()
    {
        //GIVEN
        $oPage = AM_Model_Db_Table_Abstract::factory('page_imposition')->findOneBy('id', 1);

        $oMapper = AM_Mapper_Abstract::factory($oPage, "sqlite", array('adapter' => $this->_oAdapter));
        /* @var $oMapper AM_Mapper_Sqlite_Page */

        //WHEN
        $oMapper->unmap();

        //THEN
        $oGivenDataSet    = $this->_oConnectionMock->createQueryTable('page_imposition', 'SELECT page_id, is_linked_to, position_type FROM page_imposition ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/PageImpositionMapperSqliteTest.xml')
                              ->getTable('page_imposition');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
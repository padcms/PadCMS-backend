<?php
/**
 * @author vl4dimir
 */
class ElementWithResourceMapperSqliteTest extends AM_Test_PHPUnit_DatabaseTestCase
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
                . DIRECTORY_SEPARATOR . 'ElementWithResourceMapperSqliteTest.yml';
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
CREATE TABLE `element`
(
       id INTEGER NOT NULL,
       page_id INTEGER NOT NULL,
       element_type_name TEXT NOT NULL,
       weight INTEGER NOT NULL,
       content_text  BLOB,
       PRIMARY KEY(id)
);

CREATE TABLE `element_data`
(
       id INTEGER NOT NULL,
       element_id INTEGER NOT NULL,
       type TEXT NOT NULL,
       value TEXT NOT NULL,
       position_id  INTEGER,
       PRIMARY KEY(id)
);

CREATE TABLE `element_data_position`
(
       id INTEGER,
       start_x INTEGER,
       start_y INTEGER,
       end_x INTEGER,
       end_y INTEGER,
       PRIMARY KEY(id)
);
SQL;
        $this->_oAdapter->getConnection()->exec($sSql);

        $this->_oConnectionMock = $this->createZendDbConnection($this->_oAdapter, $sDbName);

        return $this;
    }

    public function testShouldUnmapElement()
    {
        //GIVEN
        $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', 1);

        $oMapper = AM_Mapper_Abstract::factory($oElement, "sqlite", array('adapter' => $this->_oAdapter));
        /* @var $oMapper AM_Mapper_Sqlite_Page */

        //WHEN
        $oMapper->unmap();

        //THEN
        //Element
        $oGivenDataSet    = $this->_oConnectionMock->createQueryTable('element', 'SELECT id, page_id, element_type_name, weight, content_text FROM element ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/ElementWithResourceMapperSqliteTest.xml')
                              ->getTable('element');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);

        //Element data
        $oGivenDataSet    = $this->_oConnectionMock->createQueryTable('element_data', 'SELECT element_id, type, value FROM element_data ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/ElementWithResourceMapperSqliteTest.xml')
                              ->getTable('element_data');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
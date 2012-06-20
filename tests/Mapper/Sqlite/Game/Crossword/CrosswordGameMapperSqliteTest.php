<?php
/**
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

class CrosswordGameMapperSqliteTest extends AM_Test_PHPUnit_DatabaseTestCase
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
                . DIRECTORY_SEPARATOR . 'CrosswordGameMapperSqliteTest.yml';
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

CREATE TABLE `game_crossword`
(
       id INTEGER NOT NULL,
       title TEXT,
       grid_width INTEGER,
       grid_height INTEGER,
       PRIMARY KEY(id)
);

CREATE TABLE `game_crossword_word`
(
       id INTEGER NOT NULL,
       game INTEGER NOT NULL,
       answer TEXT NOT NULL,
       question TEXT NOT NULL,
       length INTEGER DEFAULT NULL,
       direction  TEXT NOT NULL,
       start_from INTEGER NOT NULL,
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

        $oMapper = AM_Mapper_Abstract::factory($oElement, 'sqlite', array('adapter' => $this->_oAdapter));
        /* @var $oMapper AM_Mapper_Sqlite_Page */

        //WHEN
        $oMapper->unmap();

        //THEN
        //Element
        $oGivenDataSet    = $this->_oConnectionMock->createQueryTable('element', 'SELECT id, page_id, element_type_name, weight, content_text FROM element ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/CrosswordGameMapperSqliteTest.xml')
                              ->getTable('element');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);

        //Element data
        $oGivenDataSet    = $this->_oConnectionMock->createQueryTable('element_data', 'SELECT element_id, type, value FROM element_data ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/CrosswordGameMapperSqliteTest.xml')
                              ->getTable('element_data');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);

        //Game
        $oGivenDataSet    = $this->_oConnectionMock->createQueryTable('game_crossword', 'SELECT id, grid_width, grid_height FROM game_crossword ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/CrosswordGameMapperSqliteTest.xml')
                              ->getTable('game_crossword');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);

        //Crossword word
        $oGivenDataSet    = $this->_oConnectionMock->createQueryTable('game_crossword_word', 'SELECT game, answer, question, length, direction, start_from FROM game_crossword_word ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/CrosswordGameMapperSqliteTest.xml')
                              ->getTable('game_crossword_word');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
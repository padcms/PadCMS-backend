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

class TermMoveToTerm extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'TermMoveToTerm.yml';
    }

    public function testShouldMoveDownInSameRoot()
    {
        //GIVEN
        $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(array('id' => 1));

        //WHEN
        AM_Model_Db_Table_Abstract::factory('term')->moveTerm($oTerm, 0, 3);

        //THEN
        $oGivenDataSet    = $this->getConnection()->createQueryTable('term', 'SELECT id, position, parent_term FROM term ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/TermMoveToTerm/testShouldMoveDownInSameRoot.xml')
                              ->getTable('term');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }

    public function testShouldMoveUpInSameRoot()
    {
        //GIVEN
        $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(array('id' => 3));

        //WHEN
        AM_Model_Db_Table_Abstract::factory('term')->moveTerm($oTerm, 0, 1);

        //THEN
        $oGivenDataSet    = $this->getConnection()->createQueryTable('term', 'SELECT id, position, parent_term FROM term ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/TermMoveToTerm/testShouldMoveUpInSameRoot.xml')
                              ->getTable('term');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }

    public function testShouldMoveBetweenInSameRoot()
    {
        //GIVEN
        $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(array('id' => 3));

        //WHEN
        AM_Model_Db_Table_Abstract::factory('term')->moveTerm($oTerm, 0, 2);

        //THEN
        $oGivenDataSet    = $this->getConnection()->createQueryTable('term', 'SELECT id, position, parent_term FROM term ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/TermMoveToTerm/testShouldMoveBetweenInSameRoot.xml')
                              ->getTable('term');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }

    public function testShouldMoveUpInDifferentRoot()
    {
        //GIVEN
        $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(array('id' => 1));

        //WHEN
        AM_Model_Db_Table_Abstract::factory('term')->moveTerm($oTerm, 2, 1);

        //THEN
        $oGivenDataSet    = $this->getConnection()->createQueryTable('term', 'SELECT id, position, parent_term FROM term ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/TermMoveToTerm/testShouldMoveUpInDifferentRoot.xml')
                              ->getTable('term');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }

    public function testShouldMoveBetweenInDifferentRoot()
    {
        //GIVEN
        $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(array('id' => 1));

        //WHEN
        AM_Model_Db_Table_Abstract::factory('term')->moveTerm($oTerm, 2, 2);

        //THEN
        $oGivenDataSet    = $this->getConnection()->createQueryTable('term', 'SELECT id, position, parent_term FROM term ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/TermMoveToTerm/testShouldMoveBetweenInDifferentRoot.xml')
                              ->getTable('term');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}

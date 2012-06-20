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

class TermUpdateRelationsTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('term');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function testShouldUpdateTermsRelations()
    {
        //GIVEN
        $termRootData = array("id" => 1, "vocabulary" => 1, "revision" => 1);
        $this->termRoot = new AM_Model_Db_Term();
        $this->termRoot->setFromArray($termRootData);

        $termChildData = array("id" => 2, "vocabulary" => 1, "revision" => 1);
        $this->termChild = new AM_Model_Db_Term();
        $this->termChild->setFromArray($termChildData);
        $this->termChild->save();

        $this->termRoot->addChild($this->termChild);

        //WHEN
        $this->termChild->updateReletations();

        //THEN
        $this->assertEquals(1, $this->termChild->parent_term, "Wrong parent term");

        $queryTable    = $this->getConnection()->createQueryTable("term", "SELECT id, vocabulary, revision, parent_term FROM term ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/updateRelations.xml")
                              ->getTable("term");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

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
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

class PageMoveToRevisionTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('page');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $pageData = array("id" => 1, "title" => "test_page", "revision" => 1, "user" => 1);
        $this->page = new AM_Model_Db_Page();
        $this->page->setFromArray($pageData);
        $this->page->save();
    }

    public function testShouldMoveToRevision()
    {
        //GIVEN
        $revisionData = array("id" => 2, "title" => "test_revision2", "state" => 1, "issue" => 2, "user" => 2);
        $revisionTo = new AM_Model_Db_Revision();
        $revisionTo->setFromArray($revisionData);

        //WHEN
        $this->page->moveToRevision($revisionTo);

        //THEN
        $this->assertEquals(2, $this->page->user, "User id should change");
        $this->assertEquals(2, $this->page->revision, "Revision id should change");

        $queryTable    = $this->getConnection()->createQueryTable("page", "SELECT id, user, revision FROM page ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/move2revision.xml")
                              ->getTable("page");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

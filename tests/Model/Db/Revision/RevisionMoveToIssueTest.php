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

class RevisionMoveToIssueTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('revision');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $revisionData = array("id" => 1, "title" => "test_revision", "state" => 1, "issue" => 1, "user" => 1);
        $this->revision = new AM_Model_Db_Revision();
        $this->revision->setFromArray($revisionData);
        $this->revision->save();

        $appData = array("id" => 1, "title" => "test_app1", "client" => 1);
        $this->app     = new AM_Model_Db_Application(array("data" => $appData));

        $issueData = array("id" => 1, "title" => "test_issue1", "user" => 1);
        $issue     = new AM_Model_Db_Issue(array("data" => $issueData));
        $this->revision->setApplication($this->app);
        $this->revision->setIssue($issue);

        $this->pageMock = $this->getMock('AM_Model_Db_Page', array('moveToRevision'), array(), '', false);
        $this->revision->setPages(array($this->pageMock));
    }

    public function testShouldMoveToIssue()
    {
        //GIVEN
        $appData = array("id" => 2, "title" => "test_app2", "client" => 2);
        $app     = new AM_Model_Db_Application(array("data" => $appData));

        $issueData = array("id" => 2, "title" => "test_issue", "user" => 2);
        $issue = new AM_Model_Db_Issue(array("data" => $issueData));
        $issue->setApplication($app);

        //THEN
        $this->pageMock->expects($this->once())
                        ->method('moveToRevision');

        //WHEN
        $this->revision->moveToIssue($issue);
        $this->revision->refresh();

        //THEN
        $this->assertEquals(2, $this->revision->issue, "Issue id should change");
        $this->assertEquals(2, $this->revision->user, "User id should change");

        $queryTable    = $this->getConnection()->createQueryTable("revision", "SELECT id, user, issue FROM revision ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/move2issue.xml")
                              ->getTable("revision");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testShouldNotMoveToSameIssue()
    {
        //GIVEN
        $issueData = array("id" => 1, "title" => "test_issue1", "user" => 1, "application" => 1);
        $issue = new AM_Model_Db_Issue(array("data" => $issueData));
        $issue->setApplication($this->app);

        //THEN
        $this->pageMock->expects($this->never())
                        ->method('moveToRevision');

        //WHEN
        $this->revision->moveToIssue($issue);
        $this->revision->refresh();

        //THEN
        $this->assertEquals(1, $this->revision->issue, "Issue id should not change");
        $this->assertEquals(1, $this->revision->user, "User id should not change");

        $queryTable    = $this->getConnection()->createQueryTable("revision", "SELECT id, user, issue FROM revision ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/not_move2issue.xml")
                              ->getTable("revision");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

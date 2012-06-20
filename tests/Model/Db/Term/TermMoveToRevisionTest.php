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

class TermMoveToRevisionTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('term');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        //Given
        $vocabularyTOCData = array("id" => 1, "has_hierarchy" => 1, "multiple" => 0);
        $this->vocabularyTOC = new AM_Model_Db_Vocabulary();
        $this->vocabularyTOC->setFromArray($vocabularyTOCData);

        $vocabularyTagData = array("id" => 2, "has_hierarchy" => 0, "multiple" => 1);
        $this->vocabularyTag = new AM_Model_Db_Vocabulary();
        $this->vocabularyTag->setFromArray($vocabularyTagData);

        //Move to
        $vocabularyTOCData = array("id" => 3, "has_hierarchy" => 1, "multiple" => 0);
        $this->vocabularyTOCNew = new AM_Model_Db_Vocabulary();
        $this->vocabularyTOCNew->setFromArray($vocabularyTOCData);

        $vocabularyTagData = array("id" => 4, "has_hierarchy" => 0, "multiple" => 1);
        $this->vocabularyTagNew = new AM_Model_Db_Vocabulary();
        $this->vocabularyTagNew->setFromArray($vocabularyTagData);

        $appData = array("id" => 2, "title" => "test_app1", "client" => 1);
        $app     = new AM_Model_Db_Application(array("data" => $appData));
        $app->setVocabularyToc($this->vocabularyTOCNew);
        $app->setVocabularyTag($this->vocabularyTagNew);

        $issueData = array("id" => 2, "title" => "test_issue1", "user" => 1);
        $issue     = new AM_Model_Db_Issue(array("data" => $issueData));

        $revisionData = array("id" => 2, "title" => "test_revision", "state" => 1, "issue" => 2, "user" => 1);
        $this->revision = new AM_Model_Db_Revision();
        $this->revision->setFromArray($revisionData);
        $this->revision->setApplication($app);
        $this->revision->setIssue($issue);
    }

    public function testShouldMoveTOCTermToRevision()
    {
        //GIVEN
        $termTOCData = array("id" => 1, "vocabulary" => 1, "revision" => 1);
        $this->termTOC = new AM_Model_Db_Term();
        $this->termTOC->setFromArray($termTOCData);
        $this->termTOC->save();
        $this->termTOC->setVocabulary($this->vocabularyTOC);

        //WHEN
        $this->termTOC->moveToRevision($this->revision);

        //THEN
        $this->assertEquals(3, $this->termTOC->vocabulary, "Vocabulary should change");
        $this->assertEquals(2, $this->termTOC->revision, "Revision should change");

        $queryTable    = $this->getConnection()->createQueryTable("term", "SELECT id, vocabulary, revision FROM term ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/moveTOC2Rrevision.xml")
                              ->getTable("term");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testShouldMoveTagTermToRevision()
    {
        //GIVEN
        $termTOCData = array("id" => 1, "vocabulary" => 2, "revision" => null);
        $this->termTag = new AM_Model_Db_Term();
        $this->termTag->setFromArray($termTOCData);
        $this->termTag->save();
        $this->termTag->setVocabulary($this->vocabularyTag);

        //WHEN
        $this->termTag->moveToRevision($this->revision);

        //THEN
        $this->assertEquals(4, $this->termTag->vocabulary, "Vocabulary should change");
        $this->assertEquals(null, $this->termTag->revision, "Revision should not change");

        $queryTable    = $this->getConnection()->createQueryTable("term", "SELECT id, vocabulary FROM term ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/moveTag2Rrevision.xml")
                              ->getTable("term");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

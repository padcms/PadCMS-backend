<?php

class TermCopyToRevisionTest extends AM_Test_PHPUnit_DatabaseTestCase
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

        //Copy to
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

    public function testShouldCopyTOCTermToRevision()
    {
        //GIVEN
        $termTOCData = array("id" => 1, "vocabulary" => 1, "revision" => 1);
        $this->termTOC = new AM_Model_Db_Term();
        $this->termTOC->setFromArray($termTOCData);
        $this->termTOC->save();
        $this->termTOC->setVocabulary($this->vocabularyTOC);

        $this->resourceMock = $this->getMock('AM_Model_Db_Term_Data_Resource', array('copy'), array($this->termTOC));
        $this->termTOC->setResources($this->resourceMock);

        //THEN
        $this->resourceMock->expects($this->once())
            ->method('copy');

        //WHEN
        $this->termTOC->copyToRevision($this->revision);

        //THEN
        $this->assertEquals(3, $this->termTOC->vocabulary, "Vocabulary should change");
        $this->assertEquals(2, $this->termTOC->revision, "Revision should change");

        $queryTable    = $this->getConnection()->createQueryTable("term", "SELECT id, vocabulary, revision FROM term ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copyTOC2Rrevision.xml")
                              ->getTable("term");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testShouldCopyTagTermToRevision()
    {
        //GIVEN
        $termTOCData = array("id" => 1, "vocabulary" => 2, "revision" => null);
        $this->termTag = new AM_Model_Db_Term();
        $this->termTag->setFromArray($termTOCData);
        $this->termTag->save();
        $this->termTag->setVocabulary($this->vocabularyTag);

        $this->resourceMock = $this->getMock('AM_Model_Db_Term_Data_Resource', array('copy'), array($this->termTag));
        $this->termTag->setResources($this->resourceMock);

        //THEN
        $this->resourceMock->expects($this->once())
            ->method('copy');

        //WHEN
        $this->termTag->copyToRevision($this->revision);

        //THEN
        $this->assertEquals(4, $this->termTag->vocabulary, "Vocabulary should change");
        $this->assertEquals(null, $this->termTag->revision, "Revision should not change");

        $queryTable    = $this->getConnection()->createQueryTable("term", "SELECT id, vocabulary FROM term ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copyTag2Rrevision.xml")
                              ->getTable("term");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

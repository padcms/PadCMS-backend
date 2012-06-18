<?php

class RevisionCopyFromRevisionTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('revision', 'page');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $revisionData = array("id" => 1, "title" => "test_revision", "state" => 1, "issue" => 1, "user" => 1);
        $this->revision = new AM_Model_Db_Revision();
        $this->revision->setFromArray($revisionData);

        $this->vocabularyTOCMock = $this->getMock('AM_Model_Db_Vocabulary', array('copyToRevision'), array(array("readOnly" => true, "table" => new AM_Model_Db_Table_Vocabulary())));
        $this->vocabularyTagMock = $this->getMock('AM_Model_Db_Vocabulary', array('copyToRevision'), array(array("readOnly" => true, "table" => new AM_Model_Db_Table_Vocabulary())));

        $appData = array("id" => 1, "title" => "test_app1", "client" => 1);
        $app     = new AM_Model_Db_Application(array("data" => $appData));
        $app->setVocabularyToc($this->vocabularyTOCMock);
        $app->setVocabularyTag($this->vocabularyTagMock);

        $issueData = array("id" => 1, "title" => "test_issue1", "user" => 1);
        $issue     = new AM_Model_Db_Issue(array("data" => $issueData));
        $this->revision->setApplication($app);
        $this->revision->setIssue($issue);
    }

    public function testShouldCopyFromRevision()
    {
        //GIVEN
        $revisionData = array("id" => 2, "title" => "test_revision2", "state" => 1, "issue" => 2, "user" => 2);
        $revisionNew = new AM_Model_Db_Revision();
        $revisionNew->setFromArray($revisionData);

        //THEN
        $this->vocabularyTOCMock->expects($this->once())
                        ->method('copyToRevision')
                        ->with($this->equalTo($revisionNew));

        $this->vocabularyTagMock->expects($this->once())
                        ->method('copyToRevision')
                        ->with($this->equalTo($revisionNew));

        //WHEN
        $revisionNew->copyFromRevision($this->revision);
    }
}

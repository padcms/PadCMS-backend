<?php

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

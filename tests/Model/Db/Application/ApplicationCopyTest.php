<?php

class ApplicationCopyTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('application');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $appData = array("id" => 1, "title" => "test_app", "client" => 1);
        $this->app = new AM_Model_Db_Application();
        $this->app->setFromArray($appData);
        $this->app->save();

        $this->issueSetMock = $this->getMock('AM_Model_Db_Rowset_Issue', array('copyToUser'), array(array("readOnly" => true)));
        $this->app->setIssues($this->issueSetMock);
    }

    public function testShouldCopyToUser()
    {
        //GIVEN
        $userData = array("id" => 2, "login" => "test_user", "client" => 2);
        $user = new AM_Model_Db_User(array("data" => $userData));

        //THEN
        $this->issueSetMock->expects($this->once())
            ->method('copyToUser')
            ->with($this->equalTo($user));

        //WHEN
        $this->app->copyToUser($user);
        $this->app->refresh();

        //THEN
        $this->assertEquals(2, $this->app->client, "Client id should change");

        $queryTable    = $this->getConnection()->createQueryTable("application", "SELECT id, client FROM application ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy.xml")
                              ->getTable("application");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

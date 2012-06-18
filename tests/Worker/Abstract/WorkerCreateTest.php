<?php

class WorkerCreateTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function getDataSet()
    {
        $tableNames = array('task', 'task_type');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $mockType = new AM_Model_Db_TaskType();
        $mockType->class = 'AM_Task_Worker_Mock';
        $mockType->save();

    }

    public function testShouldCreateWorkerTask()
    {
       //GIVEN
        $worker = new AM_Task_Worker_Mock();
        $worker->addOption('key', 'value');

        //WHEN
        $worker->create();

        //THEN
        $queryTable    = $this->getConnection()->createQueryTable("task", "SELECT id, task_type_id, status, options FROM task ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/create.xml")
                              ->getTable("task");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

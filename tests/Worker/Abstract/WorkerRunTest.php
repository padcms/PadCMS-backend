<?php

class WorkerRunTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var AM_Model_Db_Task **/
    protected $_taskRecord          = null;

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

        $this->_taskRecord = new AM_Model_Db_Task();
        $this->_taskRecord->task_type_id = $mockType->id;
        $this->_taskRecord->status       = AM_Task_Worker_Abstract::STATUS_NEW;
        $this->_taskRecord->save();
    }

    public function testShouldRunWorkerTask()
    {
       //GIVEN
        $worker = new AM_Task_Worker_Mock();
        $worker->setTask($this->_taskRecord);

        //WHEN
        $worker->run();

        //THEN
        $queryTable    = $this->getConnection()->createQueryTable("task", "SELECT id, task_type_id, status, options FROM task ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/run.xml")
                              ->getTable("task");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

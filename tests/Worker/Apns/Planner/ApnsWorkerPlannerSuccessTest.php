<?php

class ApnsWorkerPlannerSuccessTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var AM_Task_Worker_AppleNotification_Planner **/
    protected $_worker          = null;
    /** @var Zend_Config **/
    protected $_config          = null;


    public function getDataSet()
    {
        $tableNames = array('task', 'task_type', 'application', 'issue', 'device_token');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $taskType = new AM_Model_Db_TaskType();
        $taskType->setFromArray(array('id' => 1, 'class' => 'AM_Task_Worker_AppleNotification_Planner'))->save();

        $taskType = new AM_Model_Db_TaskType();
        $taskType->setFromArray(array('id' => 2, 'class' => 'AM_Task_Worker_AppleNotification_Sender'))->save();

        $token = new AM_Model_Db_DeviceToken();
        $token->setFromArray(array('udid' => '123', 'token' => '456', 'application_id' => 1))->save();

        $application = new AM_Model_Db_Application();
        $application->setFromArray(array('id' => 1, 'client' => 1))->save();

        $issue = new AM_Model_Db_Issue();
        $issue->setFromArray(array('id' => 1, 'application' => 1, 'user' => 1))->save();

        $this->_worker = new AM_Task_Worker_AppleNotification_Planner();
        $this->_worker->addOption('issue_id', 1);
        $this->_worker->addOption('message', 'Test message');
        $this->_worker->addOption('badge', 0);
        $this->_worker->create();
    }

    public function testShouldPlaneNotification()
    {
        //WHEN
        try {
            $this->_worker->run();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        //THEN
        $queryTable    = $this->getConnection()->createQueryTable('task', 'SELECT id, task_type_id, status, options FROM task ORDER BY id');
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/create.xml')
                              ->getTable('task');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

<?php

class ApnsWorkerFeedbackTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $taskType = new AM_Model_Db_TaskType();
        $taskType->setFromArray(array('id' => 1, 'class' => 'AM_Task_Worker_AppleNotification_Feedback'))->save();
    }

    public function testShouldGetFeedback()
    {
        //GIVEN
        $this->_worker = new AM_Task_Worker_AppleNotification_Feedback();
        $this->_worker->addOption('application_id', 11);
        $this->_worker->create();

        //WHEN
        try {
            $this->_worker->run();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}

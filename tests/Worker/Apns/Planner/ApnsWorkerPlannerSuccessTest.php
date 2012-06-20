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

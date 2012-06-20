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

class ApnsWorkerSenderSuccessTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var AM_Task_Worker_AppleNotification_Sender **/
    protected $_worker          = null;
    protected $_standardMock    = null;
    /** @var Zend_Config **/
    protected $_config          = null;


    public function getDataSet()
    {
        $tableNames = array('task', 'task_type');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $this->_standardMock = $this->getMock("AM_Tools_Standard", array('fwrite', 'fread', 'stream_select',
                                                                        'stream_set_write_buffer', 'stream_set_blocking',
                                                                        'stream_socket_client', 'stream_context_create', 'fclose',
                                                                        'is_resource', 'is_readable'));

        $taskType = new AM_Model_Db_TaskType();
        $taskType->class = 'AM_Task_Worker_AppleNotification_Sender';
        $taskType->save();

        $config = Zend_Registry::get('config');
        //Create new apns configuration
        $apns = array('environment' => 'sandbox', 'cerificate_path' => 'test_path');
        $this->_config = new Zend_Config($config->toArray(), true);
        $this->_config->apns = $apns;
        Zend_Registry::set('config', $this->_config);
    }

    public function testShouldSendNotification()
    {
        //GIVEN
        $this->_worker = new AM_Task_Worker_AppleNotification_Sender();
        $this->_worker->addOption('message', 'Test message');
        $this->_worker->addOption('application_id', 11);
        $this->_worker->addOption('tokens', array('1e82db91c7ceddd72bf33d74ae052ac9c84a065b35148ac401388843106a7485'));
        $this->_worker->addOption('badge', 0);
        $this->_worker->create();

        //THEN
        //Checking certificates
        $this->_standardMock->expects($this->at(0))
             ->method('is_readable')
             ->with($this->equalTo('test_path/11_sandbox.pem'))
             ->will($this->returnValue(true));

        //Connecting to APNS
        $this->_standardMock->expects($this->any())
             ->method('stream_context_create')
             ->will($this->returnValue(true));

        $this->_standardMock->expects($this->any())
             ->method('stream_socket_client')
             ->will($this->returnValue(true));

        //Sending binary notofication
        $this->_standardMock->expects($this->any())
             ->method('fwrite')
             ->will($this->returnValue(77));
        //Checking error response from apns
        $this->_standardMock->expects($this->any())
             ->method('fread')
             ->will($this->returnValue(pack('CCN', 8, 0, 1)));

        //WHEN
        try {
            $this->_worker->run();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}

<?php
/**
 * Send test push message
 * @ingroup AM_Cli
 */
class AM_Cli_Task_TestPush extends AM_Cli_Task_Abstract
{

    protected function _configure()
    {
      $this->addOption('issue',   'i', '=i', 'Issue id');
      $this->addOption('udid',    'u', '=s', 'Device token');
      $this->addOption('message', 'm', '=s', 'Message text');
    }

    public function execute()
    {
      $iIssueId  = intval($this->_getOption('issue'));
      $sUdid   = $this->_getOption('udid');
      $sMessage = $this->_getOption('message');

      $oIssue = AM_Model_Db_Table_Abstract::factory('issue')
              ->findOneBy(array('id' => $iIssueId));
      if (!$oIssue->getIterator()->count()) {
        throw new Exception('Issue not found.');
      }
      $aIssue = $oIssue->toArray();

      var_dump($aIssue['application']);

      $oDeviceToken = AM_Model_Db_Table_Abstract::factory('device_token')
              ->findOneBy(array('application_id' => $aIssue['application'], 'udid' => $sUdid), NULL, array('created DESC'));
      if (!$oDeviceToken->getIterator()->count()) {
        throw new Exception('Device token not found.');
      }
      $aDeviceToken = $oDeviceToken->toArray();


      $oTaskPlanner = new AM_Task_Worker_AppleNotification_PlannerTest();
      $oTaskPlanner->setOptions(array('issue_id' => $iIssueId,
                                      'message'  => $sMessage,
                                      'badge'    => 1,
                                      'token'    => $aDeviceToken['token']))
                   ->create();
    }
}

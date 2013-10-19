<?php
/**
 * Send test push message
 * @ingroup AM_Cli
 */
class AM_Cli_Task_TestPushBoxcar extends AM_Cli_Task_Abstract
{
  protected function _configure()
  {
    $this->addOption('issue', 'i', '=i', 'Issue id');
    $this->addOption('token', 't', '=s', 'Device token');
    //$this->addOption('device', 'd', '=d', 'Device');
    $this->addOption('message', 'm', '=s', 'Message text');
  }

  public function execute()
  {
    $iIssueId  = intval($this->_getOption('issue'));
    $sToken   = $this->_getOption('token');
    //$sDevice   = $this->_getOption('device');
    $sMessage = $this->_getOption('message');

//    if (empty($sToken) && !empty($sDevice)) {
//        $oDeviceToken = AM_Model_Db_Table_Abstract::factory('device_token')->findOneBy('udid', $sDevice);
//        $sToken = $oDeviceToken->token;
//    }

    $oTaskPlanner = new AM_Task_Worker_Notification_Planner_BoxcarTest();
    $oTaskPlanner->setOptions(array('issue_id' => $iIssueId,
                                    'message'  => $sMessage,
                                    'badge'    => 1,
                                    'token'    => $sToken))
                 ->create();
  }
}

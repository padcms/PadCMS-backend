<?php
/**
 * Send test push message
 * @ingroup AM_Cli
 */
class AM_Cli_Task_TestPush extends AM_Cli_Task_Abstract
{
  protected function _configure()
  {
    $this->addOption('issue', 'i', '=i', 'Issue id');
    $this->addOption('token', 't', '=s', 'Device token');
    $this->addOption('message', 'm', '=s', 'Message text');
  }

  public function execute()
  {
    $iIssueId  = intval($this->_getOption('token'));
    $sToken   = $this->_getOption('token');
    $sMessage = $this->_getOption('message');

    $oTaskPlanner = new AM_Task_Worker_Notification_PlannerTest_Apple();
    $oTaskPlanner->setOptions(array('issue_id' => $iIssueId,
                                    'message'  => $sMessage,
                                    'badge'    => 1,
                                    'token'    => $sToken))
                 ->create();
  }
}

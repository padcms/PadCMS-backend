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
        $this->addOption('message', 'm', '=s', 'Message text');
    }

    public function execute()
    {
        $iIssueId  = intval($this->_getOption('issue'));
        $sMessage = $this->_getOption('message');

        $oTaskPlanner = AM_Task_Worker_Notification_Planner_Abstract::createTask(array('issue_id' => $iIssueId,
                                                                                       'message'  => $sMessage,
                                                                                       'badge'    => 1));
        $oTaskPlanner->run();
    }
}

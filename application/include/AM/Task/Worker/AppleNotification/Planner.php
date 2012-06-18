<?php
/**
 * @file
 * AM_Task_Worker_AppleNotification_Planner class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Task for planning push notification sending
 * @ingroup AM_Task
 */
class AM_Task_Worker_AppleNotification_Planner extends AM_Task_Worker_Abstract
{
    /**
     * @see AM_Task_Worker_Abstract::_fire()
     * @throws AM_Task_Worker_Exception
     * @return void
     */
    protected function _fire()
    {
        $iIssueId = intval($this->getOption('issue_id'));
        $sMessage = $this->getOption('message');
        $iBadge   = intval($this->getOption('badge'));

        $oIssue = AM_Model_Db_Table_Abstract::factory('issue')
                ->findOneBy('id', $iIssueId);
        /* @var $oIssue AM_Model_Db_Issue */

        if (is_null($oIssue)) {
            throw new AM_Task_Worker_Exception('Issue not found');
        }

        $iApplicationId = $oIssue->getApplication()->id;

        if (empty($iApplicationId)) {
            throw new AM_Task_Worker_Exception('Wrong parameters were given');
        }

        $oDevices = AM_Model_Db_Table_Abstract::factory('device_token')
                ->findAllBy(array('application_id' => $iApplicationId));

        if (0 === $oDevices->count()) {
            $this->finish();
            $this->getLogger()->debug('There are not devices to notificate');
            return;
        }

        $aSenderTaskOptions = array('message' => $sMessage, 'badge' => $iBadge, 'application_id' => $iApplicationId);

        $oTaskSender = new AM_Task_Worker_AppleNotification_Sender();

        $aDevices = array_chunk($oDevices->toArray(), 1000);

        foreach ($aDevices as $aDeviceSlice) {
            $aTokens = array();
            foreach ($aDeviceSlice as $aDevice) {
                $this->getLogger()->debug(sprintf('Prepearing message for token \'%s\'', $aDevice["token"]));
                $aTokens[] = $aDevice['token'];
            }
            $aSenderTaskOptions['tokens'] = $aTokens;
            $oTaskSender->setOptions($aSenderTaskOptions);
            $oTaskSender->create();
        }
    }
}
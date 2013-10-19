<?php
/**
 * @file
 * AM_Task_Worker_Notification_Planner_Apple class definition.
 *
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

/**
 * Task for planning push notification sending
 * @ingroup AM_Task
 */
class AM_Task_Worker_Notification_Planner_Boxcar extends AM_Task_Worker_Abstract
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

        $oTokensApple = AM_Model_Db_Table_Abstract::factory('device_token')->getTokens($iApplicationId, AM_Model_Pns_Type::PLATFORM_IOS);

        $oTokensAndroid = AM_Model_Db_Table_Abstract::factory('device_token')->getTokens($iApplicationId, AM_Model_Pns_Type::PLATFORM_ANDROID);

        if (0 === $oTokensApple->count() && 0 === $oTokensAndroid->count()) {
            $this->finish();
            $this->getLogger()->debug('There are not tokens to notificate');
            return;
        }

        $aSenderTaskOptions = array('message' => $sMessage, 'badge' => $iBadge, 'application_id' => $iApplicationId);

        $aTokensApple = array_chunk($oTokensApple->toArray(), 100);
        $aTokensAndroid = array_chunk($oTokensAndroid->toArray(), 100);

        $iMoreTokenSliceCount = (count($aTokensApple) > count($aTokensAndroid)) ? count($aTokensApple) : count($aTokensAndroid);

        for ($i = 0; $i < $iMoreTokenSliceCount; $i++) {
            $aTokensAndroidPrepared = array();
            $aTokensApplePrepared   = array();

            if (!empty($aTokensAndroid[$i]) && is_array($aTokensAndroid[$i])) {
                foreach ($aTokensAndroid[$i] as $aTokenAndroid) {
                    $this->getLogger()->debug(sprintf('Preparing message for token android \'%s\'', $aTokenAndroid['token']));
                    $aTokensAndroidPrepared[] = $aTokenAndroid['token'];
                }
            }

            if (!empty($aTokensApple[$i]) && is_array($aTokensApple[$i])) {
                foreach ($aTokensApple[$i] as $aTokenApple) {
                    $this->getLogger()->debug(sprintf('Preparing message for token apple \'%s\'', $aTokenApple['token']));
                    $aTokensApplePrepared[] = $aTokenApple['token'];
                }
            }

            $aSenderTaskOptions['tokens_apple'] = $aTokensApplePrepared;
            $aSenderTaskOptions['tokens_android'] = $aTokensAndroidPrepared;
            $oTaskSender = new AM_Task_Worker_Notification_Sender_Boxcar();
            $oTaskSender->setOptions($aSenderTaskOptions);
            $oTaskSender->create();
        }
    }
}

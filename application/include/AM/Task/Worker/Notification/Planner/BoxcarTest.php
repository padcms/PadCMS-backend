<?php
/**
 * @file
 * AM_Task_Worker_Notification_Planner_AppleTest class definition.
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
class AM_Task_Worker_Notification_Planner_BoxcarTest extends AM_Task_Worker_Abstract
{
    /**
     * @see AM_Task_Worker_Abstract::_fire()
     * @throws AM_Task_Worker_Exception
     * @return void
     */
    protected function _fire()
    {
        $iIssueId     = intval($this->getOption('issue_id'));
        $sMessage     = $this->getOption('message');
        $iBadge       = intval($this->getOption('badge'));
        $sDeviceToken = $this->getOption('token');

        $this->getLogger()->debug('Token value %s', $sDeviceToken);

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

        $oToken = AM_Model_Db_Table_Abstract::factory('device_token')->findOneBy(
            array(
                'token' => $sDeviceToken,
                'application_id' => $iApplicationId
            )
        );

        $aSenderTaskOptions = array(
          'message' => $sMessage,
          'badge' => $iBadge,
          'application_id' => $iApplicationId
        );

        $aSenderTaskOptions['tokens_apple']   = array();
        $aSenderTaskOptions['tokens_android'] = array();

        if ($oToken->type_os == 'ios') {
            $aSenderTaskOptions['tokens_apple'] = array($sDeviceToken);
        }
        else {
            $aSenderTaskOptions['tokens_android'] = array($sDeviceToken);
        }

        $oTaskSender = new AM_Task_Worker_Notification_Sender_Boxcar();
        $oTaskSender->setOptions($aSenderTaskOptions);
        $oTaskSender->create();
    }
}

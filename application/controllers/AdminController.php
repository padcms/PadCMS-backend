<?php
/**
 * @file
 * AdminController class definition.
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
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Controller_Action
 */
class AdminController extends AM_Controller_Action
{
    /**
     * Get users list for copy/move dialog
     */
    public function transferDialogUsersAction()
    {
        try {
            if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $sEntity   = $this->_getParam('entity');
            $iClientId = $this->_getParam('clientId');

            if (empty($sEntity) || empty($iClientId)) {
                throw new AM_Controller_Exception_BadRequest('Invalid parameters');
            }

            $oUserTable = AM_Model_Db_Table_Abstract::factory('user');
            /* @var $oUserTable AM_Model_Db_Table_User */

            $aUsers = array();

            switch ($sEntity) {
                case 'application':
                    $oUsers = $oUserTable->findAllBy(array('client' => $iClientId));
                    if (count($oUsers)) {
                        $aUsers = $oUsers->toArray();
                    }
                    break;
                case 'issue':
                    $oUsers = $oUserTable->findAllWithAppsByClientId($iClientId);
                    if (count($oUsers)) {
                        $aUsers = $oUsers->toArray();
                    }
                    break;
                case 'revision':
                    $oUsers = $oUserTable->findAllWithIssuesByClientId($iClientId);
                    if (count($oUsers)) {
                        $aUsers = $oUsers->toArray();
                    }
                    break;
                default:
                    throw new AM_Controller_Exception_BadRequest(sprintf('Wrong entity "%s"', $sEntity));
            }

            if (!count($aUsers)) {
                $aMessage = array('error' => '404', 'message' => 'Users not found');
                return $this->getHelper('Json')->sendJson($aMessage);
            } else {
                return $this->getHelper('Json')->sendJson($aUsers);
            }
        } catch (Exception $e) {
            $aMessage = array('error' => $e->getCode(), 'message' => $e->getMessage());
            return $this->getHelper('Json')->sendJson($aMessage);
        }
    }

    /**
     * Get applications list for copy/move dialog
     */
    public function transferDialogAppsAction()
    {
        try {
            if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
                throw new AM_Exception("Access denied");
            }

            $sEntity   = $this->_getParam('entity');
            $iClientId = $this->_getParam('clientId');
            $iUserId   = $this->_getParam('userId');

            if (empty($sEntity) || empty($iClientId)) {
                throw new AM_Controller_Exception_BadRequest('Invalid parameters');
            }

            $oApplicationTable = AM_Model_Db_Table_Abstract::factory('application');
            /* @var $oApplicationTable AM_Model_Db_Table_Application */

            $aApplications = array();

            switch ($sEntity) {
                case 'issue':
                    $oApplications = $oApplicationTable->findAllByUsertId($iUserId);
                    if (count($oApplications)) {
                        $aApplications = $oApplications->toArray();
                    }
                    break;
                case 'revision':
                    $oApplications = $oApplicationTable->findAllWithIssuesByUserId($iUserId);
                    if (count($oApplications)) {
                        $aApplications = $oApplications->toArray();
                    }
                    break;
                default:
                    throw new AM_Controller_Exception_BadRequest(sprintf('Wrong entity "%s"', $sEntity));
            }

            if (!count($aApplications)) {
                $aMessage = array('error' => '404', 'message' => 'Applications not found');
                return $this->getHelper('Json')->sendJson($aMessage);
            } else {
                return $this->getHelper('Json')->sendJson($aApplications);
            }
        } catch (Exception $e) {
            $aMessage = array ('error' => $e->getCode(), 'message' => $e->getMessage());
            return $this->getHelper('Json')->sendJson($aMessage);
        }
    }

    /**
     * Get applications list for copy/move dialog
     */
    public function transferDialogIssuesAction()
    {
        try {
            if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $sEntity         = $this->_getParam('entity');
            $iApplicationId  = $this->_getParam('appId');
            $iUserId         = $this->_getParam('userId');

            if (empty($sEntity) || empty($iApplicationId)) {
                throw new AM_Controller_Exception_BadRequest('Invalid parameters');
            }

            $oIssueTable = AM_Model_Db_Table_Abstract::factory('issue');
            /** @var $usersTable AM_Model_Db_Table_Issue **/

            $aIssues = array();

            switch ($sEntity) {
                case 'revision':
                    $oIssues = $oIssueTable->findAllByApplicationIdAndUser($iApplicationId, $iUserId);
                    if (count($oIssues)) {
                        $aIssues = $oIssues->toArray();
                    }
                    break;
                default:
                    throw new AM_Controller_Exception_BadRequest(sprintf('Wrong entity "%s"', $sEntity));
            }

            if (!count($aIssues)) {
                $aMessage = array('error' => '404', 'message' => 'Issues not found');
                return $this->getHelper('Json')->sendJson($aMessage);
            } else {
                return $this->getHelper('Json')->sendJson($aIssues);
            }
        } catch (Exception $e) {
            $aMessage = array ('error' => $e->getCode(), 'message' => $e->getMessage());
            return $this->getHelper('Json')->sendJson($aMessage);
        }
    }

    /**
     * Init copy/move dialog
     */
    public function transferDialogAction()
    {
        try {
            if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $sEntity = $this->_getParam('entity');

            if (empty($sEntity)) {
                throw new AM_Controller_Exception_BadRequest('Invalid parameters');
            }

            $oClientTable = AM_Model_Db_Table_Abstract::factory('client');
            /** @var $usersTable AM_Model_Db_Table_Client **/

            $this->view->clients = array();

            switch ($sEntity) {
                case 'application':
                    $oClients = $oClientTable->findAllWithUsers();
                    if (count($oClients)) {
                        $this->view->clients = $oClients->toArray();
                    }
                    break;
                case 'issue':
                    $oClients = $oClientTable->findAllWithUsersAndApps();
                    if (count($oClients)) {
                        $this->view->clients = $oClients->toArray();
                    }
                    break;
                case 'revision':
                    $oClients = $oClientTable->findAllWithUsersAndAppsWithIssues();
                    if (count($oClients)) {
                        $this->view->clients = $oClients->toArray();
                    }
                    break;
                default:
                    throw new AM_Controller_Exception_BadRequest(sprintf('Wrong entity "%s"', $sEntity));
            }
            if (!count($this->view->clients)) {
                $this->view->message = array('message' => 'Clients not found');
            }
        } catch (Exception $e) {
            $this->view->message = array('error' => $e->getCode(), 'message' => $e->getMessage());
        }
    }
}
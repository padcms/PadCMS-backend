<?php
/**
 * @file
 * ApplicationController class definition.
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
 * @ingroup AM_Controller_Action
 */
class ApplicationController extends AM_Controller_Action
{
    /** @var AM_View_Helper_Breadcrumbs **/
    public $oBreadCrumb = null; /**< @type AM_View_Helper_Breadcrumbs */

    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->_aUserInfo['client'] && $this->_aUserInfo['role'] != 'admin') {
            $this->getRequest()->setParam('cid', $this->_aUserInfo['client']);
        }

        $this->oBreadCrumb = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                                                           AM_View_Helper_Breadcrumbs::APP, $this->_getAllParams());
    }

    public function postDispatch()
    {
        $this->oBreadCrumb->show();
        parent::postDispatch();
    }

    public function indexAction()
    {
        return $this->_forward('list');
    }

    /**
     * Add application action
     */
    public function addAction()
    {
        if ($this->_aUserInfo['role'] == 'admin') {
            $iClientId = $this->_getParam('cid');
        } else {
            $iClientId = $this->_aUserInfo['client'];
        }

        if (!$iClientId) {
            throw new AM_Controller_Exception_BadRequest('Invalid parameters');
        }

        $iApplicationId = intval($this->_getParam('aid'));

        if ($iApplicationId && !AM_Model_Db_Table_Abstract::factory('application')->checkAccess($iApplicationId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $sClass = AM_Component_Record_Database_Application_Abstract::getClassByApplicationId($iApplicationId);
        /* @var string */

        $oComponent = new $sClass($this, 'application', $iApplicationId, $iClientId);
        if ($oComponent->operation()) {
            $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $oComponent->getPrimaryKeyValue());
            /* @var $oApplication AM_Model_Db_Application */
            $lstIssues = $oApplication->getIssues();
            foreach ($lstIssues as $oIssue) {
                /* @var $oIssue AM_Model_Db_Issue */
                $oIssue->exportRevisions();
            }

            if (is_a($oComponent, 'AM_Component_Record_Database_Application_Add')) {
                return $this->_redirect("/application/edit/aid/{$oApplication->id}/cid/$iClientId");
            }
            else {
                return $this->_redirect('/application/list/cid/' . $iClientId);
            }

        }

        $oComponent->show();
    }

    /**
     * Edit application action
     */
    public function editAction()
    {
        $this->_forward('add');
    }

    /**
     * List application action
     */
    public function listAction()
    {
        if ($this->_aUserInfo['role'] == 'admin') {
            $iClientId = $this->_getParam('cid');
        } else {
            $iClientId = $this->_aUserInfo['client'];
        }

        if (!$iClientId) {
            throw new AM_Controller_Exception_BadRequest();
        }

        $oGrid = new AM_Component_List_Application($this, $iClientId);
        $oGrid->show();

        $oPager = new AM_Component_Pager($this, 'pager', $oGrid);
        $oPager->show();

        $this->view->clientId = $iClientId;
    }

    /**
     * Delete application action
     */
    public function deleteAction()
    {
        $iApplicationId = intval($this->_getParam('aid'));

        if (!AM_Model_Db_Table_Abstract::factory('application')->checkAccess($iApplicationId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $iApplicationId);
        $oApplication->delete();

        $this->_redirect('/application/list/cid/' . $oApplication->client);
    }

    /**
     * Action for move/copy application
     */
    public function transferAction()
    {
        $aMessage = array('status' => 0, 'message' => '');

        try {

            if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $iApplicationId = $this->_getParam('entityId');
            $iClientId      = $this->_getParam('clientId');
            $iUserId        = $this->_getParam('userId');
            $sMethod        = $this->_getParam('method');

            if (empty($iApplicationId) || empty($iUserId) || empty($iClientId) || empty($sMethod)) {
               throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
            }

            $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $iApplicationId);
            $oUser        = AM_Model_Db_Table_Abstract::factory('user')->findOneBy('id', $iUserId);
            $oClient      = AM_Model_Db_Table_Abstract::factory('client')->findOneBy('id', $iClientId);

            if (is_null($oApplication) || is_null($oUser) || is_null($oClient) || $oUser->client != $oClient->id) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
            }

            $sMethod = $sMethod . 'ToUser';
            if (!method_exists($oApplication, $sMethod)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Method "%s" hasn\'t been defined in the Application object', $sMethod));
            }
            $oApplication->$sMethod($oUser);

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage['message'] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }
}
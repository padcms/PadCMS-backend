<?php
/**
 * @file
 * ApplicationController class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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

        $oComponent = new AM_Component_Record_Database_Application($this, 'application', $iApplicationId, $iClientId);
        if ($oComponent->operation()) {
            $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $oComponent->getPrimaryKeyValue());
            /* @var $oApplication AM_Model_Db_Application */
            $lstIssues = $oApplication->getIssues();
            foreach ($lstIssues as $oIssue) {
                /* @var $oIssue AM_Model_Db_Issue */
                $oIssue->exportRevisions();
            }

            return $this->_redirect('/application/list/cid/' . $iClientId);
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
            $aMessage['message'] = 'Error. ' . $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }
}
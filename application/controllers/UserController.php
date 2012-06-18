<?php
/**
 * @file
 * UserController class definition.
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
class UserController extends AM_Controller_Action
{
    protected $iClientId = null; /**< @type int */
    protected $iUserId   = null; /**< @type int */

    public function preDispatch() {
        parent::preDispatch();

        if ($this->_aUserInfo['role'] == 'admin') {
            $this->iClientId = intval($this->_getParam('cid'));
        } else {
            $this->iClientId = $this->_aUserInfo['client'];
        }

        $this->iUserId = intval($this->_getParam('uid'));

        if ($this->iUserId && empty($this->iUserId)) {
            throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
        }

        if ($this->iUserId && !AM_Model_Db_Table_Abstract::factory('user')->checkAccess($this->iUserId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $this->oHelperBreadCrumb = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                                                           AM_View_Helper_Breadcrumbs::USER, $this->_getAllParams());

        $this->view->clientId = $this->iClientId;
    }

    public function postDispatch()
    {
        $this->oHelperBreadCrumb->show();
        parent::postDispatch();
    }

    /*
     * User index action
     */
    public function indexAction()
    {
        return $this->_aUserInfo['is_admin'] ? $this->_forward('add') : $this->_forward('show');
    }

    /*
     * User list action
     */
    public function listAction()
    {
        $oComponentGrid = new AM_Component_List_User($this, $this->iClientId);
        $oComponentGrid->show();

        $oComponentPager = new AM_Component_Pager($this, 'pager', $oComponentGrid);
        $oComponentPager->show();
    }

    /*
     * User add action
     */
    public function addAction()
    {
        $oComponentRecord = new AM_Component_Record_Database_User($this, 'user', $this->iUserId, $this->iClientId);
        if ($oComponentRecord->operation()) {
            return $this->_redirect('/user/list/cid/' . $this->iClientId);
        }

        $oComponentRecord->show();
    }

    /*
     * User edit action
     */
    public function editAction()
    {
        $this->_forward('add');
    }

    /*
     * User delete action
     */
    public function deleteAction()
    {
        $oUser = AM_Model_Db_Table_Abstract::factory('user')->findOneBy(array('id' => $this->iUserId));
        $oUser->delete();

        $this->_redirect('/user/list/cid/' . $this->iClientId);
    }

    /*
     * User show action
     */
    public function showAction()
    {
        $oUser = AM_Model_Db_Table_Abstract::factory('user')->findOneBy(array('id' => $this->iUserId));
        /* @var $oUser AM_Model_Db_User */
        if (is_null($oUser)) {
            throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
        }

        $aUserProfile            = $oUser->toArray();
        $aUserProfile['title']   = $oUser->getClient()->title;
        $this->view->userProfile = $aUserProfile;
    }
}
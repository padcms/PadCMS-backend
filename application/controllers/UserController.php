<?php
/**
 * @file
 * UserController class definition.
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
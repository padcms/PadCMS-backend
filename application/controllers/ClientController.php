<?php
/**
 * @file
 * ClientController class definition.
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
class ClientController extends AM_Controller_Action
{

    /** @var AM_View_Helper_Breadcrumbs **/
    public $oBreadCrumb = null; /**< @type AM_View_Helper_Breadcrumbs */

    public function preDispatch() {
        parent::preDispatch();

        if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $this->oBreadCrumb = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                                                           AM_View_Helper_Breadcrumbs::CLIENT, $this->_getAllParams());
    }

    public function postDispatch()
    {
        $this->oBreadCrumb->show();
        parent::postDispatch();
    }

    /**
     * Client index action
     */
    public function indexAction()
    {
        return $this->_forward('list');
    }

    /**
     * Client edit action
     */
    public function editAction()
    {
        return $this->_forward('add');
    }

    /**
     * Client add action
     */
    public function addAction()
    {
        $iClientId = intval($this->_getParam('cid'));

        $oComponentClient = new AM_Component_Record_Database_Client($this, 'client', $iClientId);
        if ($oComponentClient->operation()) {
            return $this->_redirect('/');
        }
        $oComponentClient->show();
    }

    /**
     * Client list action
     */
    public function listAction()
    {
        $oGrid = new AM_Component_List_Client($this);
        $oGrid->show();

        $oPager = new AM_Component_Pager($this, 'pager', $oGrid);
        $oPager->show();
    }

    /**
     * Client delete action
     */
    public function deleteAction()
    {
        $iClientId = intval($this->_getParam('cid'));

        $oClient = AM_Model_Db_Table_Abstract::factory('client')->findOneBy('id', $iClientId);
        if (is_null($oClient)) {
            throw new AM_Controller_Exception_BadRequest('Invalid parameters');
        }
        $oClient->delete();

        $this->_redirect('/client/list');
    }
}
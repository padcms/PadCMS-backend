<?php
/**
 * @file
 * DevicesController class definition.
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
class DevicesController extends AM_Controller_Action
{
    public function preDispatch() {
        parent::preDispatch();

        if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $this->oBreadCrumb = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                                                           AM_View_Helper_Breadcrumbs::CLIENT, $this->_getAllParams());
    }

    /**
     * Device index action
     */
    public function indexAction()
    {
        return $this->_forward('list');
    }

    /**
     * Device list action
     */
    public function listAction()
    {
        $oComponentFilter = new AM_Component_Filter($this, 'filter', array(
            'controls' => array(
                'identifer' => array(
                    'title' => 'UDID',
                ),
                'linked' => array(
                    'title' => 'Only linked to users',
                )
            )
        ));

        if ($oComponentFilter->operation()) {
            return $this->_redirect($this->getHelper('Url')->url($oComponentFilter->getUrlParams(), null, true));
        }
        $oComponentFilter->show();

        $oGrid = new AM_Component_List_Devices($this, 'grid', $oComponentFilter);
        $oGrid->show();

        $oPager = new AM_Component_Pager($this, 'pager', $oGrid);
        $oPager->show();
    }

    /**
     * Device edit action
     */
    public function editAction()
    {
        $iDeviceId = intval($this->_getParam('id'));

        $oComponentDevice = new AM_Component_Record_Database_Device($this, 'device', $iDeviceId);
        if ($oComponentDevice->operation()) {
            return $this->_redirect('/devices/list');
        }

        $oComponentDevice->show();
    }

    /**
     * Device delete action
     */
    public function deleteAction()
    {
        $iDeviceId = intval($this->_getParam('id'));

        $oDevice = AM_Model_Db_Table_Abstract::factory('device')->findOneBy('id', $iDeviceId);
        if (is_null($oDevice)) {
            throw new AM_Controller_Exception_BadRequest('Invalid parameters');
        }
        $oDevice->delete();

        return $this->_redirect('/devices/list');
    }
}
<?php
/**
 * @file
 * DevicesController class definition.
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
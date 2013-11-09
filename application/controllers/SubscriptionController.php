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
class SubscriptionController extends AM_Controller_Action
{
    /** @var AM_View_Helper_Breadcrumbs **/
    public $oBreadCrumb = null; /**< @type AM_View_Helper_Breadcrumbs */

    /** @var int Application id **/
    protected $iApplicationId = null; /**< @type int */


    public function preDispatch()
    {
        parent::preDispatch();

//        if ($this->_aUserInfo['client'] && $this->_aUserInfo['role'] != 'admin') {
//            $this->getRequest()->setParam('cid', $this->_aUserInfo['client']);
//        }
        $this->iApplicationId = intval($this->_getParam('aid'));
        $iSubscriptionId = intval($this->_getParam('sid'));
        if (empty($this->iApplicationId) && !empty($iSubscriptionId)) {
            $oSubscription = AM_Model_Db_Table_Abstract::factory('subscription')->findOneBy('id', $iSubscriptionId);
            if (!empty($oSubscription)) {
                $this->iApplicationId = $oSubscription->application;
            }
        }



//        $this->oBreadCrumb = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
//                                                           AM_View_Helper_Breadcrumbs::APP, $this->_getAllParams());
    }

    public function postDispatch()
    {
        //$this->oBreadCrumb->show();
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
        $iSubscriptionId = intval($this->_getParam('sid'));

        $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $this->iApplicationId);

        $oComponent = new AM_Component_Record_Database_Subscription($this, 'subscription', $iSubscriptionId, $oApplication->id);
        if ($oComponent->operation()) {
            $oSubscription = AM_Model_Db_Table_Abstract::factory('subscription')->findOneBy('id', $oComponent->getPrimaryKeyValue());

            return $this->_redirect('/subscription/list/aid/' . $oSubscription->application);
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
        $oGridComponent = new AM_Component_List_Subscription($this, $this->iApplicationId);
        $oGridComponent->show();

        $oPagerComponent = new AM_Component_Pager($this, 'pager', $oGridComponent);
        $oPagerComponent->show();

        $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $this->iApplicationId);

        $this->view->iAppId = $this->iApplicationId;
        $this->view->iClientId = $oApplication->client;
        $this->view->aAppTitle = $oApplication->title;
    }

    /**
     * Delete application action
     */
    public function deleteAction()
    {
        $iSubscriptionId = intval($this->_getParam('sid'));

        $oSubscription = AM_Model_Db_Table_Abstract::factory('subscription')->findOneBy('id', $iSubscriptionId);
        $oSubscription->delete();

        $this->_redirect('/subscription/list/aid/' . $this->iApplicationId);
    }
}

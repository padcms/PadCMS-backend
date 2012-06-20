<?php
/**
 * @file
 * RevisionController class definition.
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
class RevisionController extends AM_Controller_Action
{
    protected $iIssueId          = null; /**< @type int */
    public    $oHelperBreadCrumb = null; /**< @type AM_View_Helper_Breadcrumbs */

    public function preDispatch() {
        parent::preDispatch();

        $this->iIssueId = intval($this->_getParam('iid'));

        if ($this->iIssueId && !AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($this->iIssueId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $this->oHelperBreadCrumb = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                                                           AM_View_Helper_Breadcrumbs::REV, $this->_getAllParams());

        $this->view->issueId = $this->iIssueId;
    }

    public function postDispatch()
    {
        $this->oHelperBreadCrumb->show();
        parent::postDispatch();
    }

    /*
     * Revision list action
     */
    public function listAction()
    {
        $oComponentGrid = new AM_Component_List_Revision($this, $this->iIssueId);
        $oComponentGrid->show();

        $oComponetPager = new AM_Component_Pager($this, 'pager', $oComponentGrid);
        $oComponetPager->show();
    }

    /*
     * Revision add action
     */
    public function addAction()
    {
        $iRevisionId = intval($this->_getParam('rid'));

        if ($iRevisionId && !AM_Model_Db_Table_Abstract::factory('revision')->checkAccess($iRevisionId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $oComponentRecordRevision = new AM_Component_Record_Database_Revision($this, 'revision', $iRevisionId, $this->iIssueId);
        if ($oComponentRecordRevision->operation()) {
            $iRevisionId = $oComponentRecordRevision->getPrimaryKeyValue();
            $oRevision   = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy(array('id' => $iRevisionId));
            if (is_null($oRevision)) {
                throw new AM_Controller_Exception(sprintf('Can\'t find revision by id "%d"', $iRevisionId));
            }
            $oRevision->exportRevision();
            $this->_redirect('/revision/list/iid/' . $this->iIssueId);
        }
        $oComponentRecordRevision->show();
    }

    /*
     * Revision edit action
     */
    public function editAction()
    {
        $this->_forward('add');
    }

    /*
     * Revision publish action
     */
    public function publishAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iRevisionId = intval($this->_getParam('revision'));

            if (!AM_Model_Db_Table_Abstract::factory('revision')->checkAccess($iRevisionId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy(array('id' => $iRevisionId));
            if (is_null($oRevision)) {
                throw new AM_Controller_Exception(sprintf('Can\'t find revision by id "%d"', $iRevisionId));
            }

            if (AM_Model_Db_State::STATE_PUBLISHED != $oRevision->state) {
                // Update other published revision to archived
                AM_Model_Db_Table_Abstract::factory('revision')->moveAllPublishedToArchive($oRevision->issue);

                $oRevision->state   = AM_Model_Db_State::STATE_PUBLISHED;
                $oRevision->updated = new Zend_Db_Expr('NOW()');
                $oRevision->save();
            }

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage["message"]      = 'Error. Can\'t publish revision. ' . $e->getMessage();
            $aMessage["errorMessage"] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /*
     * Revision delete action
     */
    public function deleteAction()
    {
        $iRevisionId = intval($this->_getParam('rid'));

        if (!AM_Model_Db_Table_Abstract::factory('revision')->checkAccess($iRevisionId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy(array('id' => $iRevisionId));
        if (is_null($oRevision)) {
            throw new AM_Controller_Exception(sprintf('Can\'t find revision by id "%d"', $iRevisionId));
        }
        $oRevision->delete();

        return $this->_redirect('/revision/list/iid/' . $this->iIssueId);
    }

    /*
     * Revision transfer action
     *
     * Copy/move revision
     */
    public function transferAction()
    {
        $aMessage = array("status" => 0, "message" => "");
        try {
            if (array_key_exists("role", $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
                throw new AM_Controller_Exception_Forbidden("Access denied");
            }

            $iRevisionId    = intval($this->_getParam("entityId"));
            $iClientId      = intval($this->_getParam("clientId"));
            $iUserId        = intval($this->_getParam("userId"));
            $iApplicationId = intval($this->_getParam("aid"));
            $iIssueId       = intval($this->_getParam("iid"));
            $sMethod        = $this->_getParam("method");

            if (empty($iRevisionId) || empty($iIssueId) || empty($iUserId) || empty($iClientId) || empty($iApplicationId) || empty($sMethod)) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
            }

            if (!AM_Model_Db_Table_Abstract::factory('revision')->checkAccess($iRevisionId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy(array('id' => $iApplicationId));
            $oIssue       = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy(array('id' => $iIssueId));
            /* @var $oIssue AM_Model_Db_Issue */
            $oIssue->setApplication($oApplication);
            $oRevision    = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy(array('id' => $iRevisionId));
            $oUser        = AM_Model_Db_Table_Abstract::factory('user')->findOneBy(array('id' => $iUserId));
            $oClient      = AM_Model_Db_Table_Abstract::factory('client')->findOneBy(array('id' => $iClientId));

            if (empty($oRevision) || empty($oIssue) || empty($oApplication) || empty($oUser) || empty($oClient)
                    || $oUser->client        != $oClient->id
                    || $oApplication->client != $oClient->id
                    || $oIssue->application  != $oApplication->id
                    || $oIssue->user         != $oUser->id
            ) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
            }

            $sMethod = $sMethod . "ToIssue";
            if (!method_exists($oRevision, $sMethod)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Method "%s" hasn\'t been defined in the Revision object', $sMethod));
            }
            $oRevision->$sMethod($oIssue);

            $aMessage["status"] = 1;
        } catch (Exception $e) {
            $aMessage["message"] = 'Error. ' . $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }
}

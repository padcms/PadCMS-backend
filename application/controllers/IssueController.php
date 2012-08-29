<?php
/**
 * @file
 * IssueController class definition.
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
class IssueController extends AM_Controller_Action
{

    /** @var int Application id **/
    protected $iApplicationId = null; /**< @type int */

    public function preDispatch()
    {
        parent::preDispatch();

        $this->iApplicationId = intval($this->_getParam('aid'));

        if ($this->iApplicationId && !AM_Model_Db_Table_Abstract::factory('application')->checkAccess($this->iApplicationId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden();
        }

        $this->view->appId = $this->iApplicationId;
    }

    /*
     * Issue list action
     */
    public function listAction()
    {
        $oBreadCrumbHelper = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                                                     AM_View_Helper_Breadcrumbs::ISSUE,
                                                     $this->_getAllParams());
        $oBreadCrumbHelper->show();

        $oGridComponent = new AM_Component_List_Issue($this, $this->iApplicationId);
        $oGridComponent->show();

        $oPagerComponent = new AM_Component_Pager($this, 'pager', $oGridComponent);
        $oPagerComponent->show();
    }

    /*
     * Issue add action
     */
    public function addAction()
    {
        $iIssueId = intval($this->_getParam('iid'));

        if ($iIssueId && !AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $oComponentRecord = new AM_Component_Record_Database_Issue($this, 'issue', $iIssueId, $this->iApplicationId);
        $sResult = $oComponentRecord->operation();
        if ($sResult) {
            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $oComponentRecord->getPrimaryKeyValue());
            //Init export processes
            $oIssue->exportRevisions();
        }

        if ($sResult) {
            $this->_redirect('/issue/edit/iid/'. $oComponentRecord->getPrimaryKeyValue() .'/aid/' . $this->iApplicationId);
        }

        $oComponentRecord->show();

        $oBreadCrumbHelper = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                AM_View_Helper_Breadcrumbs::ISSUE, $this->_getAllParams());
        $oBreadCrumbHelper->show();
    }

    /*
     * Issue list action
     */
    public function editAction()
    {
        $this->_forward('add');
    }

    /*
     * Issue delete action
     */
    public function deleteAction()
    {
        $iIssueId = intval($this->_getParam('iid'));

        if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy(array('id' => $iIssueId));
        /* @var $oIssue AM_Model_Db_Issue */
        $oIssue->delete();

        return $this->_redirect('/issue/list/aid/' . $this->iApplicationId);
    }

    /*
     * Issue publish action
     */
    public function publishAction()
    {
        try {
            $aMessage = array('status' => 0);

            $iIssueId = intval($this->_getParam('issue', null));

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
            /* @var $oIssue AM_Model_Db_Issue */

            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            if (!count($oIssue->getPublishedRevisions())) {
                throw new AM_Controller_Exception('Issue has no published revision');
            }

            if (AM_Model_Db_State::STATE_PUBLISHED != $oIssue->state) {
                $oIssue->state   = AM_Model_Db_State::STATE_PUBLISHED;
                $oIssue->updated = new Zend_Db_Expr('NOw()');
                $oIssue->save();

                $sMessage     = $this->__('New issue is available');
                $oTaskPlanner = new AM_Task_Worker_AppleNotification_Planner();
                $oTaskPlanner->setOptions(array('issue_id' => $oIssue->id,
                                                'message'  => $sMessage,
                                                'badge'    => 1))
                                ->create();
            }

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage["message"]      = sprintf('%s %s', 'Error. Can\'t publish issue.', $e->getMessage());
            $aMessage["errorMessage"] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue upload action
     *
     * Upload hotizontal pdf
     */
    public function uploadAction()
    {
        try {
            $aMessage = array('status' => 0);
            $iIssueId = intval($this->_getParam('iid'));

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
            /* @var $oIssue AM_Model_Db_Issue */

            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }
            /* @var $oHorisontalPdf AM_Model_Db_StaticPdf */
            $oHorisontalPdf = $oIssue->uploadHorizontalPdf();
            //Init export processes
            $oIssue->exportRevisions();

            $aFile              = array();
            $aFile['name']      = $oHorisontalPdf->name;
            $aFile['nameShort'] = $this->getHelper('String')->cut($oHorisontalPdf->name);
            $aFile['smallUri']  = AM_Tools::getImageUrl(
                            'static-pdf-thumbnail',
                            'static-pdf',
                            $iIssueId,
                            $oHorisontalPdf->getResource()->getResourceBaseName()) . '?' . strtotime($oHorisontalPdf->updated);
            $aFile['bigUri']    = AM_Tools::getImageUrl(
                            '1024-768',
                            'static-pdf',
                            $iIssueId,
                            $oHorisontalPdf->getResource()->getResourceBaseName()) . '?' . strtotime($oHorisontalPdf->updated);

            $aMessage['file']               = $aFile;
            $aMessage['issueStaticPdfMode'] = $oIssue->static_pdf_mode;
            $aMessage['staticPdf']          = $oHorisontalPdf->id;
            $aMessage['status']             = 1;
        } catch (Exception $e) {
            if (isset($oHorisontalPdf)) {
                $oHorisontalPdf->delete();
            }
            $aMessage['message']      = 'Error. Can\'t upload file. ' . $e->getMessage();
            $aMessage['errorMessage'] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue upload-simple-pdf action
     *
     * Upload vertical pdf
     */
    public function uploadSimplePdfAction()
    {
        try {
            $aMessage = array('status' => 0);
            $iIssueId = intval($this->_getParam('iid'));

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
            /* @var $oIssue AM_Model_Db_Issue */

            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }
            /* @var $oSimplePdf AM_Model_Db_IssueSimplePdf */
            $oSimplePdf = $oIssue->uploadSimplePdf();

            $oHandlerIssue = new AM_Handler_Issue();
            $oHandlerIssue->setIssue($oIssue);
            $oHandlerIssue->createRevisionFromSimplePdf($oSimplePdf);

            $aFile              = array();
            $aFile['name']      = $oSimplePdf->name;
            $aFile['nameShort'] = $this->getHelper('String')->cut($oSimplePdf->name);
            $aFile['smallUri']  = AM_Tools::getImageUrl(
                            AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE,
                            AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE,
                            $iIssueId,
                            $oSimplePdf->getResource()->getResourceBaseName()) . '?' . strtotime($oSimplePdf->updated);
            $aFile['bigUri']    = AM_Tools::getImageUrl(
                            '768-1024',
                            AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE,
                            $iIssueId,
                            $oSimplePdf->getResource()->getResourceBaseName()) . '?' . strtotime($oSimplePdf->updated);

            $aMessage['file']      = $aFile;
            $aMessage['simplePdf'] = $oSimplePdf->id_issue;
            $aMessage['status']    = 1;
        } catch (Exception $e) {
            if (isset($oSimplePdf)) {
                $oSimplePdf->delete();
            }
            $aMessage["message"]      = 'Error. Can\'t upload file. ' . $e->getMessage();
            $aMessage["errorMessage"] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue delete-simple-pdf action
     */
    public function deleteSimplePdfAction()
    {
        try {
            $aMessage = array('status' => 0);

            $iIssueId = intval($this->_getParam('issueId'));

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden();
            }

            AM_Model_Db_Table_Abstract::factory('issue_simple_pdf')->deleteBy(array('id_issue'=>$iIssueId));

            AM_Tools::clearContent(AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE, $iIssueId);
            AM_Tools::clearResizerCache(AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE, AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE, $iIssueId);

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage["message"]      = 'Error. Can\'t delete simple pdf. ' . $e->getMessage();
            $aMessage["errorMessage"] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue save-weight action
     *
     * Save weights for static pdfs
     */
    public function saveWeightAction()
    {
        try {
            $aMessage = array('status' => 0);
            $iIssueId = intval($this->_getParam('iid'));

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden();
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $weight = $this->_getParam('weight');

            AM_Model_Db_Table_Abstract::factory('static_pdf')->setWeight($weight);
            /* @var $oIssue AM_Model_Db_Issue */
            $oIssue->compileHorizontalPdfs();
            //Init export processes
            $oIssue->exportRevisions();

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage["message"]      = 'Error. Can\'t change weight. ' . $e->getMessage();
            $aMessage["errorMessage"] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue delete-static-pdf
     */
    public function deleteStaticPdfAction()
    {
        try {
            $aMessage     = array('status' => 0);
            $iIssueId     = intval($this->_getParam('issueId'));
            $iStaticPdfId = intval($this->_getParam('staticPdf'));

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oHorizontalPdf = AM_Model_Db_Table_Abstract::factory('static_pdf')->findOneBy('id', $iStaticPdfId);
            /* @var $oHorizontalPdf AM_Model_Db_StaticPdf */
            if (is_null($oHorizontalPdf)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
            /* @var $oIssue AM_Model_Db_Issue */
            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            if ($oHorizontalPdf->issue != $oIssue->id) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oHorizontalPdf->delete();

            //Init export processes
            $oIssue->exportRevisions();

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage['message']      = 'Error. Can\'t delete static pdf. ' . $e->getMessage();
            $aMessage['errorMessage'] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue download action
     *
     * Send static pdf archive to user
     */
    public function downloadAction()
    {
        $iIssueId = intval($this->_getParam('iid'));

        $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
        /* @var $oIssue AM_Model_Db_Issue */
        if (is_null($oIssue)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $sFile = $oIssue->getHorizontalPdfsArchive();

        if (!is_null($sFile)) {
            $this->getResponse()
                    ->setHeader('Content-Length', filesize($sFile))
                    ->setHeader('Content-Disposition', 'filename=' . pathinfo($sFile, PATHINFO_BASENAME))
                    ->setHeader('Content-Type', 'application/zip')
                    ->appendBody(file_get_contents($sFile))
                    ->sendResponse();
            exit;
        }

        $this->view->error   = 1;
        $this->view->issueId = $iIssueId;
    }

    /**
     * Issue transfer action
     *
     * This action is responsible for application/issue/revision copying and moving
     */
    public function transferAction()
    {
        $aMessage = array('status' => 0, 'message' => '');

        try {
            if (array_key_exists('role', $this->_aUserInfo) && $this->_aUserInfo['role'] != 'admin') {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $iIssueId       = intval($this->_getParam('entityId'));
            $iClientId      = intval($this->_getParam('clientId'));
            $iUserId        = intval($this->_getParam('userId'));
            $iApplicationId = intval($this->_getParam('aid'));
            $sMethod        = $this->_getParam('method');

            if (empty($iIssueId) || empty($iUserId) || empty($iClientId) || empty($iApplicationId) || empty($sMethod)) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
            }

            $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy(array('id' => $iApplicationId));
            $oIssue       = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy(array('id' => $iIssueId));
            /* @var $oIssue AM_Model_Db_Issue */
            $oUser        = AM_Model_Db_Table_Abstract::factory('user')->findOneBy(array('id' => $iUserId));
            $oClient      = AM_Model_Db_Table_Abstract::factory('client')->findOneBy(array('id' => $iClientId));

            if (empty($oIssue) || empty($oApplication) || empty($oUser) || empty($oClient)
                    || $oUser->client != $oClient->id
                    || $oApplication->client != $oClient->id
            ) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
            }

            $sMethod = $sMethod . 'ToUserApplication';
            if (!method_exists($oIssue, $sMethod)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Method "%s" hasn\'t been defined in the Issue object', $sMethod));
            }
            $oIssue->$sMethod($oUser, $oApplication);

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage['message'] = 'Error. ' . $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue upload-help-page action
     *
     * Upload help page for issue
     */
    public function uploadHelpPageAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iIssueId      = intval($this->_getParam('iid'));
            $sHelpPageType = (string) $this->_getParam('type');

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
            /* @var $oIssue AM_Model_Db_Issue */

            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }
            $oHelpPage = $oIssue->uploadHelpPage($sHelpPageType);
            /* @var $oHelpPage AM_Model_Db_IssueHelpPage */

            $aFile              = array();
            $aFile['name']      = $oHelpPage->name;
            $aFile['nameShort'] = $this->getHelper('String')->cut($oHelpPage->name, 12);
            $aFile['smallUri']  = AM_Tools::getImageUrl(
                            $oHelpPage->getThumbnailPresetType(),
                            $oHelpPage->getThumbnailPresetType(),
                            $iIssueId,
                            $oHelpPage->getResource()->getResourceBaseName()) . '?' . strtotime($oHelpPage->updated);
            $aFile['bigUri']    = AM_Tools::getImageUrl(
                            $oHelpPage->getResolutionForPreview(),
                            $oHelpPage->getThumbnailPresetType(),
                            $iIssueId,
                            $oHelpPage->getResource()->getResourceBaseName()) . '?' . strtotime($oHelpPage->updated);

            $aMessage['file']      = $aFile;
            $aMessage['status']    = 1;
        } catch (Exception $e) {
            if (isset($oHelpPage)) {
                $oHelpPage->delete();
            }
            $aMessage['message']      = 'Error. Can\'t upload file. ' . $e->getMessage();
            $aMessage['errorMessage'] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Issue delete-help-page action
     */
    public function deleteHelpPageAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iIssueId      = intval($this->_getParam('issueId'));
            $sHelpPageType = (string) $this->_getParam('type');

            if (!AM_Model_Db_Table_Abstract::factory('issue')->checkAccess($iIssueId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
            /* @var $oIssue AM_Model_Db_Issue */
            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oHelpPage = AM_Model_Db_Table_Abstract::factory('issue_help_page')->findOneBy(array('id_issue' => $iIssueId, 'type' => $sHelpPageType));
            /* @var $oHelpPage AM_Model_Db_IssueHelpPage */
            if (!is_null($oHelpPage)) {
                $oHelpPage->delete();
            }

            //Init export processes
            $oIssue->exportRevisions();

            $aMessage['status'] = 1;
        } catch (Exception $e) {
            $aMessage['message']      = 'Error. Can\'t delete help page. ' . $e->getMessage();
            $aMessage['errorMessage'] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }
}

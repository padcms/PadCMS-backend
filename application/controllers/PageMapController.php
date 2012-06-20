<?php
/**
 * @file
 * PageMapController class definition.
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
class PageMapController extends AM_Controller_Action
{
    /**
     * PageMap index action
     */
    public function indexAction()
    {
        $this->_forward('show');
    }

    /**
     * PageMap show action
     *
     * Displays root page
     */
    public function showAction()
    {
        $iRevisionId = intval($this->_getParam('rid'));

        if (!AM_Model_Db_Table_Abstract::factory('revision')->checkAccess($iRevisionId, $this->_aUserInfo)) {
            throw new AM_Controller_Exception_Forbidden('Access denied');
        }

        $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy(array('id' => $iRevisionId));
        if (is_null($oRevision)) {
            throw new AM_Controller_Exception(sprintf('Can\'t find revision by id "%d"', $iRevisionId));
        }

        $oPageRoot = $oRevision->getPageRoot();

        if (is_null($oPageRoot)) {
            throw new AM_Controller_Exception('Revision has no root page');
        }
        $this->view->orientation = $oRevision->getIssue()->orientation;
        $this->view->rootItem    = AM_Handler_Page::parsePage($oPageRoot);
        $this->view->rid         = $oRevision->id;
        $this->view->root        = $oPageRoot->id;

        $this->oHelperBreadCrumb = new AM_View_Helper_Breadcrumbs($this->view, $this->oDb, $this->getUser(),
                                                           AM_View_Helper_Breadcrumbs::PAGE, $this->_getAllParams());
        $this->oHelperBreadCrumb->show();

        switch (isset($_COOKIE['editor-panel-place']) ? $_COOKIE['editor-panel-place'] : null) {
            case 'left':
            case 'right':
                $this->view->panel_place = $_COOKIE['editor-panel-place'];
                break;

            default:
                $this->view->panel_place = 'right';
                break;
        }
    }

    /**
     * PageMap get-page action
     */
    public function getPageAction()
    {
        $aMessage = array('success' => false);

        try {
            $iRevisionId  = intval($this->_getParam('rid'));
            $iPageId      = intval($this->_getParam('pid'));

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => $iPageId));
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given. Page not found.');
            }

            $aMessage['page']    = AM_Handler_Page::parsePage($oPage);
            $aMessage['pid']     = $oPage->id;
            $aMessage['success'] = true;

        } catch (Exception $oException) {
            $aMessage['message'] = 'Error. ' . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage, true);
    }

    /**
     * PageMap full-expand action
     */
    public function fullExpandAction()
    {
        $aResult = array('result' => false);
        try {
            $iRevisionId  = intval($this->_getParam('rid'));
            $iPageId      = intval($this->_getParam('pid'));
            $sLinkType    = (string) $this->_getParam('type');

            if (!AM_Model_Db_Table_Abstract::factory('revision')->checkAccess($iRevisionId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy(array('id' => $iRevisionId));
            /* @var $oRevision AM_Model_Db_Revision */
            if (is_null($oRevision)) {
                throw new AM_Controller_Exception(sprintf('Can\'t find revision by id "%d"', $iRevisionId));
            }

            $aPages = $oRevision->getPages();

            if (!array_key_exists($iPageId, $aPages)) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given. Page not found.');
            }
            /* @var $oPage AM_Model_Db_Page */
            $oPage = $aPages[$iPageId];

            $oHandler          = new AM_Handler_Page();
            $aResult['top']    = $oHandler->getBranch($oPage, AM_Model_Db_Page::LINK_TOP);
            $aResult['bottom'] = $oHandler->getBranch($oPage, AM_Model_Db_Page::LINK_BOTTOM);
            $aResult['left']   = $oHandler->getBranch($oPage, AM_Model_Db_Page::LINK_LEFT);
            $aResult['right']  = $oHandler->getBranch($oPage, AM_Model_Db_Page::LINK_RIGHT);
            $aResult['page']   = AM_Handler_Page::parsePage($oPage);
            $aResult['result'] = true;
        } catch (Exception $oException) {
            $aResult['message'] = 'Error. ' . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aResult, true);
    }

    /**
     * PageMap get-templates action
     *
     * Gets templates list for new page or for replacement
     */
    public function getTemplatesAction()
    {
        $sConnectionType = (string) $this->_getParam('type');
        $iPageId         = intval($this->_getParam('pid'));

        $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => $iPageId));
        /* @var $oPage AM_Model_Db_Page */

        if (is_null($oPage)) {
            throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given');
        }

        $aTemplates = array();

        if (!empty($sConnectionType)) {
            $aTemplates = $oPage->getTemplatesForConnection($sConnectionType);
        } else {
            $aTemplates = $oPage->getTemplatesForReplacement();
        }

        $this->view->templateList = $aTemplates;
    }

    /**
     * PageMap add-page action
     */
    public function addPageAction()
    {
        $aMessage = array('success' => false);

        try {
            $iRevisionId  = intval($this->_getParam('rid'));
            $iPageId      = intval($this->_getParam('pid'));
            $iTemplateId  = intval($this->_getParam('tid'));
            $sLinkType    = (string) $this->_getParam('type');
            $bBetween     = (bool) $this->_getParam('between');

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => $iPageId));
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given. Parent page not found.');
            }

            $oTemplate = AM_Model_Db_Table_Abstract::factory('template')->findOneBy(array('id' => $iTemplateId));
            /* @var $oTemplate AM_Model_Db_Template */
            if (is_null($oTemplate)) {
                throw new AM_Controller_Exception_BadRequest('Incorrect parameters were given. Template not found.');
            }

            $oPageHandler = new AM_Handler_Page();
            $oPageNew     = $oPageHandler->addPage($oPage, $oTemplate, $sLinkType, $this->getUser(), $bBetween);

            if (is_null($oPageNew)) {
                throw new AM_Controller_Exception('Can\'t add page');
            }

            $oPage->getRevision()->exportRevision();

            $aMessage['page']    = AM_Handler_Page::parsePage($oPageNew);
            $aMessage['pid']     = $oPageNew->id;
            $aMessage['success'] = true;
        } catch (Exception $oException) {
            $aMessage['message'] = 'Error. ' . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * PageMap chanhe-page-template action
     */
    public function changePageTemplateAction()
    {
        $aMessage = array('success' => false);

        try{
            $iPageId     = intval($this->_getParam('pageId'));
            $iTemplateId = intval($this->_getParam('templateId'));

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => $iPageId));
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Page with id "%d" not found', $iPageId));
            }

            $oTemplate = AM_Model_Db_Table_Abstract::factory('template')->findOneBy('id', $iTemplateId);
            /* @var $oTemplate AM_Model_Db_Template */
            if (is_null($oTemplate)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Template with id "%d" not found', $iTemplateId));
            }

            $oPage->setTemplate($oTemplate);

            $aMessage['thumbnailUri'] = $oPage->getPageBackgroundUri();

            $oPage->setUpdated(false);
            $aMessage['success'] = true;
        } catch (Exception $e) {
            $aMessage['message'] = $e->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage, true);
    }
}
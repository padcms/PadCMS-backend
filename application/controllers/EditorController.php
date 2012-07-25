<?php
/**
 * @file
 * EditorController class definition.
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
 * @ingroup AM_Page_Editor
 * @todo refactoring
 */
class EditorController extends AM_Controller_Action
{
    /**
     * Editor index action
     */
    public function indexAction()
    {
        $this->_forward('show');
    }

    /**
     * Editor delete-page action
     */
    public function deletePageAction()
    {
        $aMessage = array('result' => false);
        try {
            $iPageId = intval($this->_getParam('pid'));

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Wrong parameters were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */

            if (!$oPage->canDelete()) {
                throw new AM_Controller_Exception('Can\'t delete page');
            }
            $oPageParent = $oPage->getParent();
            /* @var $oPageParent AM_Model_Db_Page */
            $oPage->delete();

            $iPageParnetId      = null;
            if (!is_null($oPageParent)) {
                $iPageParnetId = $oPageParent->id;
                //If deleted page's had connection at the same side as parent connection, we don't have to remove connection bit on parent page
                if (!$oPage->hasConnection($oPage->getLinkType())) {
                    $oPageParent->removeConnectionBit($oPage->getLinkType());
                    $oPageParent->save();
                }
            }
            $aMessage['linkedPid'] = $iPageParnetId;
            $aMessage['result']    = true;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage, true);
    }

    /**
     * Editor show action
     */
    public function showAction()
    {
        try {
            $iPageId = intval($this->_getParam('pid'));

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);

            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oComponentEditor = new AM_Component_Editor($this, $oPage);
            $oComponentEditor->show();

            switch (isset($_COOKIE['editor-panel-place']) ? $_COOKIE['editor-panel-place'] : null) {
                case 'left':
                    $this->view->panel_place = 'right';
                    break;
                case 'right':
                default:
                    $this->view->panel_place = 'left';
            }
        } catch (Exception $oException) {
            $aMessage = array('code' => 1, 'message' => sprintf('%s %s', $this->__('Error'), $oException->getMessage()));
            return $this->getHelper('Json')->sendJson($aMessage, false);
        }
    }

    /**
     * Editor save action
     */
    public function saveAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iPageId  = intval($this->_getParam('page'));
            $sKey      = $this->_getParam('key');
            $sValue    = $this->_getParam('value');

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo) || empty($sKey)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oPage->$sKey = $sValue;
            $oPage->setUpdated(false);
            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message']      = sprintf('%s %s', $this->__('Error'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor add-tag action
     */
    public function addTagAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iPageId  = intval($this->_getParam('page'));
            $sTagName = trim($this->_getParam('tag'));

            if (mb_strlen($sTagName, 'UTF-8') > 255 || !AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oVocabulary = $oPage->getRevision()->getVocabularyTag();
            $oTag        = $oVocabulary->createTag($sTagName);
            $oTag->saveToPage($oPage);

            $oPageTags = $oPage->getTags();

            $aTagsList = array();
            foreach ($oPageTags as $oTag) {
                $aTagsList[] = array(
                    'id'    => $oTag->id,
                    'title' => $oTag->title
                );
            }
                $aMessage['tags'] = $aTagsList;

            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = $this->__('Error. Can\'t add tag') . PHP_EOL . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor delete-tag action
     */
    public function deleteTagAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iPageId = intval($this->_getParam('pid'));
            $iTagId  = intval($this->_getParam('tid'));


            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)
                    || !AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTagId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            AM_Model_Db_Table_Abstract::factory('term_page')->deleteBy(array('page' => $iPageId, 'term' => $iTagId));

            $aMessage['tag']    = $iTagId;
            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = $this->__('Error. Can\'t add tag') . PHP_EOL . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor tag-autocomplete action
     */
    public function tagAutocompleteAction()
    {
        $aTags = array();
        try {
            $iPageId  = intval($this->_getParam('pid'));
            $sTagName = trim($this->_getParam('term'));

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)
                    || empty($sTagName)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTags = AM_Model_Db_Table_Abstract::factory('term')->getTagsForAutocomplete($oPage, $sTagName);

            foreach ($oTags as $oTag) {
                $aTags[] = array(
                    'id'    => $oTag->id,
                    'value' => $oTag->title
                );
            }
        } catch (Exception $oException) {
            $oTags[] = $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aTags);
    }

    /**
     * Editor toc-get-list action
     */
    public function tocGetListAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iPageId = intval($this->_getParam('page'));

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $aToc = AM_Model_Db_Table_Abstract::factory('term')->getTocAsList($oPage->getRevision());

            $aMessage['list']    = array('' => $this->__('Nothing selected')) + $aToc;
            $aMessage['current'] = $oPage->toc;
            $aMessage['status']  = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = $this->__('Error. Can\'t get list pf terms!') . PHP_EOL . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-get-tree action
     */
    public function tocGetTreeAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iPageId        = intval($this->_getParam('page'));
            $bOnlyPermanent = (bool) $this->_getParam('onlyPermanent');

            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $aMessage['tree']   = AM_Model_Db_Table_Abstract::factory('term')->getTocAsTree($oPage->getRevision(), $bOnlyPermanent);
            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = $this->__('Error. Can\'t retrive TOC!') . PHP_EOL. $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-add action
     */
    public function tocAddAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iPageId       = intval($this->_getParam('page'));
            $iTermParentId = intval($this->_getParam('parent_id'));
            $bIsPermanent  = (bool) $this->_getParam('permanent');
            $sTitle        = trim($this->_getParam('title'));

            if(!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo) || empty($sTitle)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oRevision = null;
            if (!$bIsPermanent) {
                $oRevision = $oPage->getRevision();
            }

            $oVocabulary = $oPage->getRevision()->getVocabularyToc();
            $oTerm       = $oVocabulary->createTocTerm($sTitle, $oRevision, $iTermParentId);

            $aMessage['id'] = $oTerm->id;

            //Export revision
            $oPage->getRevision()->exportRevision();

            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Can\'t add term'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-delete action
     */
    public function tocDeleteAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iPageId    = intval($this->_getParam('page'));
            $iTocItemId = intval($this->_getParam('id'));

            if(!AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTocItemId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $iTocItemId);
            /* @var $oTerm AM_Model_Db_Term */
            if (is_null($oTerm)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm->delete();

            $oPage->getRevision()->exportRevision();

            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Can\'t delete term'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor get-static-pdf-list action
     */
    public function getStaticPdfListAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iPageId = intval($this->_getParam('pid'));

            if(!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($iPageId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneByPageId($iPageId);
            /* @var $oIssue AM_Model_Db_Issue */
            if (is_null($oIssue)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oHorizonalPages = $oIssue->getHorizontalPages();
            $aFilesList      = array();
            foreach ($oHorizonalPages as $oHorizonalPage) {
                $aFileInfo      = pathinfo($oHorizonalPage->resource);
                $sFileBaseName  = $aFileInfo['basename'];
                $sFileExtension = $aFileInfo['extension'];
                $sFileId        = $aFileInfo['filename'];

                $sImageThumb = AM_Tools::getImageUrl('204-153', 'cache-static-pdf', $oIssue->id, $sFileBaseName . '?' . strtotime($oIssue->updated));
                $sImageFull  = AM_Tools::getImageUrl('none', 'cache-static-pdf', $oIssue->id, $sFileId . '.' . $sFileExtension. '?' . strtotime($oIssue->updated));
                $aFilesList[] = array(
                    'url'         => $sImageThumb,
                    'preview_url' => $sImageFull,
                    'id'          => $oHorizonalPage->id
                );
            }

            $aMessage['status'] = 1;
            $aMessage['issue']  = $oIssue->id;
            $aMessage['list']   = $aFilesList;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error. Can\'t load pdfs list'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-rename action
     */
    public function tocRenameAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iPageId      = intval($this->_getParam('page'));
            $iTocTermId   = intval($this->_getParam('id'));
            $sTocItemName = trim($this->_getParam('title'));

            if(!AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTocTermId, $this->_aUserInfo) || empty($sTocItemName)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $iTocTermId);
            /* @var $oTerm AM_Model_Db_Term */
            if (is_null($oTerm)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm->title   = AM_Tools::filter_xss($sTocItemName);
            $oTerm->updated = new Zend_Db_Expr('NOW()');
            $oTerm->save();

            $oPage->getRevision()->exportRevision();

            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error. Can\'t rename TOC term'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-get-item action
     */
    public function tocGetItemAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iTocTermId = intval($this->_getParam('id'));

            if(!AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTocTermId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $iTocTermId);
            /* @var $oTerm AM_Model_Db_Term */
            if (is_null($oTerm)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $aItem = $oTerm->toArray();
            unset($aItem['thumb_stripe']);
            unset($aItem['thumb_summary']);

            $sUniq = '?' . strtotime($oTerm->updated);

            $aThumbStripe              = array();
            $aThumbStripe['name']      = $oTerm->thumb_stripe;
            $aThumbStripe['nameShort'] = $this->getHelper('String')->cut($oTerm->thumb_stripe);
            if (!empty($oTerm->thumb_stripe)) {
                $sThumbStripeExtension    = strtolower(pathinfo($oTerm->thumb_stripe, PATHINFO_EXTENSION));
                $aThumbStripe['smallUri'] = AM_Tools::getImageUrl('toc-stripe', 'toc', $iTocTermId, 'stripe.' . $sThumbStripeExtension) . $sUniq;
                $aThumbStripe['bigUri']   = AM_Tools::getImageUrl('none', 'toc', $iTocTermId, 'stripe.' . $sThumbStripeExtension) . $sUniq;
            } else {
                $aThumbStripe['smallUri'] = AM_Tools::getImageUrl('toc-stripe', 'toc', null, null);
            }

            $aThumbSummary              = array();
            $aThumbSummary['name']      = $oTerm->thumb_summary;
            $aThumbSummary['nameShort'] = $this->getHelper('String')->cut($oTerm->thumb_summary);
            if (!empty($oTerm->thumb_summary)) {
                $aThumbSummaryExtension    = strtolower(pathinfo($oTerm->thumb_summary, PATHINFO_EXTENSION));
                $aThumbSummary['smallUri'] = AM_Tools::getImageUrl('toc-summary', 'toc', $iTocTermId, 'summary.' . $aThumbSummaryExtension) . $sUniq;
                $aThumbSummary['bigUri']   = AM_Tools::getImageUrl('none', 'toc', $iTocTermId, 'summary.' . $aThumbSummaryExtension) . $sUniq;
            } else {
                $aThumbSummary['smallUri'] = AM_Tools::getImageUrl('toc-summary', 'toc', null, null);
            }

            $aItem['thumbStripe']  = $aThumbStripe;
            $aItem['thumbSummary'] = $aThumbSummary;

            $aMessage['tocItem'] = $aItem;
            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error. Can\'t get term data'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-save action
     */
    public function tocSaveAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iPageId      = intval($this->_getParam('page'));
            $iTocTermId   = intval($this->_getParam('id'));
            $sKey         = trim($this->_getParam('key'));
            $sValue       = trim($this->_getParam('value'));

            if (!AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTocTermId, $this->_aUserInfo) || empty($sKey)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $iTocTermId);
            /* @var $oTerm AM_Model_Db_Term */
            if (is_null($oTerm)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm->$sKey   = $sValue;
            $oTerm->updated = new Zend_Db_Expr('NOW()');
            $oTerm->save();

            $oPage->getRevision()->exportRevision();

            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error. Can\'t get term data'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-upload
     * Upload stripe ans summary for TOC's term
     * @return JSON
     */
    public function tocUploadAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iRevisionId = intval($this->_getParam('rid'));
            $iTocItemId  = intval($this->_getParam('id'));
            $sKey        = trim($this->_getParam('key'));

            if (!AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTocItemId, $this->_aUserInfo) || empty($sKey)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy('id', $iRevisionId);
            /* @var $oRevision AM_Model_Db_Revision */
            if (is_null($oRevision)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $iTocItemId);
            /* @var $oTerm AM_Model_Db_Term */
            if (is_null($oTerm)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm->getResources()->upload($sKey);

            $sField        = 'thumb_' . $sKey;
            $sResourceFile = $oTerm->$sField;

            $aFileInfo      = pathinfo($sResourceFile);
            $sFileName      = $aFileInfo['filename'];
            $sFileExtension = $aFileInfo['extension'];

            $aResourceFileViewInfo              = array();
            $aResourceFileViewInfo['name']      = $sFileName . '.' . $sFileExtension;
            $aResourceFileViewInfo['nameShort'] = $this->getHelper('String')->cut($sFileName) . '.' . $sFileExtension;

            $sFileName = $sKey . '.' . $sFileExtension;
            if (AM_Tools::isAllowedImageExtension($sFileName)) {
                $uniq                             = '?' . strtotime($oTerm->updated);
                $aResourceFileViewInfo['smallUri'] = AM_Tools::getImageUrl('toc-' . $sKey, 'toc', $oTerm->id, $sFileName) . $uniq;
                $aResourceFileViewInfo['bigUri']   = AM_Tools::getImageUrl('none', 'toc', $oTerm->id, $sFileName) . $uniq;
            } else {
                $aResourceFileViewInfo['smallUri'] = AM_Tools::getIconForNonImageFile($sFileName);
            }

            $oRevision->exportRevision();

            $aMessage['file']   = $aResourceFileViewInfo;
            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error. Can\'t upload file'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-delete-stripe action
     */
    public function tocDeleteStripeAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iRevisionId = intval($this->_getParam('rid'));
            $iTocItemId  = intval($this->_getParam('id'));

            if (!AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTocItemId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy('id', $iRevisionId);
            /* @var $oRevision AM_Model_Db_Revision */
            if (is_null($oRevision)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $iTocItemId);
            /* @var $oTerm AM_Model_Db_Term */
            if (is_null($oTerm)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            AM_Tools::clearContent(AM_Model_Db_Term_Data_Abstract::TYPE, $oTerm->id, 'stripe.*');
            AM_Tools::clearResizerCache(AM_Model_Db_Term_Data_Abstract::TYPE, AM_Model_Db_Term_Data_Abstract::TYPE, $oTerm->id, 'stripe.*');

            $oTerm->thumb_stripe = null;
            $oTerm->updated      = new Zend_Db_Expr('NOW()');
            $oTerm->save();

            $oRevision->exportRevision();

            $aDefaultImage               = array();
            $aDefaultImage['defaultUri'] = AM_Tools::getImageUrl('toc-stripe', 'toc', $iTocItemId);

            $aMessage['file']   = $aDefaultImage;
            $aMessage['status'] = 1;

        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error. Can\'t delete file'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Editor toc-delete-summary action
     */
    public function tocDeleteSummaryAction()
    {
        $aMessage = array('status' => 0);

        try {
            $iRevisionId = intval($this->_getParam('rid'));
            $iTocItemId  = intval($this->_getParam('id'));

            if (!AM_Model_Db_Table_Abstract::factory('term')->checkAccess($iTocItemId, $this->_aUserInfo)) {
                throw new AM_Controller_Exception_BadRequest('Error. Invalid params were given');
            }

            $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy('id', $iRevisionId);
            /* @var $oRevision AM_Model_Db_Revision */
            if (is_null($oRevision)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $iTocItemId);
            /* @var $oTerm AM_Model_Db_Term */
            if (is_null($oTerm)) {
                throw new AM_Controller_Exception_Forbidden('Access denied');
            }

            AM_Tools::clearContent(AM_Model_Db_Term_Data_Abstract::TYPE, $oTerm->id, 'sumary.*');
            AM_Tools::clearResizerCache(AM_Model_Db_Term_Data_Abstract::TYPE, AM_Model_Db_Term_Data_Abstract::TYPE, $oTerm->id, 'sumary.*');

            $oTerm->thumb_summary = null;
            $oTerm->updated       = new Zend_Db_Expr('NOW()');
            $oTerm->save();

            $oRevision->exportRevision();

            $aDefaultImage               = array();
            $aDefaultImage['defaultUri'] = AM_Tools::getImageUrl('toc-summary', 'toc', $iTocItemId);

            $aMessage['file']   = $aDefaultImage;
            $aMessage['status'] = 1;

        } catch (Exception $oException) {
            $aMessage['message'] = sprintf('%s %s', $this->__('Error. Can\'t delete file'), $oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }
}
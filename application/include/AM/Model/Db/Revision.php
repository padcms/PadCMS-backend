<?php
/**
 * @file
 * AM_Model_Db_Revision class definition.
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
 * Revision model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Revision extends AM_Model_Db_Abstract
{
    /** @var array **/
    protected $_aPages = null; /**< @type array */
    /** @var AM_Model_Db_Page **/
    protected $_oPageRoot = null; /**< @type AM_Model_Db_Page */
    /** @var AM_Model_Db_Application **/
    protected $_oApplication = null; /**< @type AM_Model_Db_Application */
    /** @var AM_Model_Db_Issue **/
    protected $_oIssue = null; /**< @type AM_Model_Db_Issue */

    /**
     * Copy revision to new issue
     * @param AM_Model_Db_Issue $oIssue
     * @param boolean $bInitExport Do we have to init export process
     * @return AM_Model_Db_Revision
     */
    public function copyToIssue(AM_Model_Db_Issue $oIssue, $bInitExport = true)
    {
        $aData = array();
        $aData['issue']   = $oIssue->id;
        $aData['user']    = $oIssue->user;
        $aData['created'] = null;
        $aData['updated'] = null;

        $iIdOld = $this->id;

        $oRevisionCopy = $this->copy($aData);
        /* @var $oRevisionCopy AM_Model_Db_Revision */

        $oRevisionCopy->setApplication($oIssue->getApplication());
        $oRevisionCopy->setIssue($oIssue);

        $oRevisionFrom = $this->getTable()->findOneBy('id', $iIdOld);
        $oRevisionCopy->copyFromRevision($oRevisionFrom);

        if ($bInitExport) {
            $this->exportRevision();
        }

        return $this;
    }

    /**
     * Move revision to new issue
     * @param AM_Model_Db_Issue $oIssue
     * @param boolean $bInitExport Do we have to init export process
     * @return AM_Model_Db_Revision
     */
    public function moveToIssue(AM_Model_Db_Issue $oIssue, $bInitExport = true)
    {
        if($oIssue->id == $this->issue && $oIssue->user == $this->user && $oIssue->application == $this->getApplication()->id) {
            return;
        }

        $oVocabularyToc = $this->getVocabularyToc();
        $oVocabularyTag = $this->getVocabularyTag();
        $aPages         = $this->getPages();

        $this->issue = $oIssue->id;
        $this->user  = $oIssue->user;
        $this->save();
        $this->setIssue($oIssue);
        $this->setApplication($oIssue->getApplication());

        if (!empty($oVocabularyToc)) {
            $oVocabularyToc->moveToRevision($this);
        }

        if (!empty($oVocabularyTag)) {
            $oVocabularyTag->moveToRevision($this);
        }

        foreach ($aPages as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oPage->moveToRevision($this);
        }

        if ($bInitExport) {
            $this->exportRevision();
        }

        return $this;
    }

    /**
     * Copy all revision data (page map, elemtnts) from one to other
     * @param AM_Model_Db_Revision $oRevisionFrom
     * @return AM_Model_Db_Revision
     */
    public function copyFromRevision(AM_Model_Db_Revision $oRevisionFrom)
    {
        $oVocabularyToc = $oRevisionFrom->getVocabularyToc();
        $oVocabularyTag = $oRevisionFrom->getVocabularyTag();
        $aPages         = $oRevisionFrom->getPages();

        if (!empty($oVocabularyToc)) {
            $oVocabularyToc->copyToRevision($this, $oRevisionFrom);
        }

        if (!empty($oVocabularyTag)) {
            $oVocabularyTag->copyToRevision($this, $oRevisionFrom);
        }

        foreach ($aPages as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oPage->copyToRevision($this);
        }

        foreach ($aPages as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oPage->savePageImposition();
        }

        return $this;
    }

    /**
     * Returns root page object
     *
     * @return AM_Model_Db_Page
     */
    public function getPageRoot()
    {
        if (is_null($this->_oPageRoot)) {
            $this->_oPageRoot = AM_Model_Db_Table_Abstract::factory('page')
                    ->findOneBy(array('revision' => $this->id, 'template' => AM_Model_Db_Template::TPL_COVER_PAGE, 'deleted' => 'no'));
        }

        return $this->_oPageRoot;
    }

    /**
     * Set revision pages
     * @param array $aPages
     * @return AM_Model_Db_Revision
     */
    public function setPages($aPages)
    {
        $this->_aPages = (array) $aPages;

        return $this;
    }

    /**
     * Get revision pages
     * @return array
     */
    public function getPages()
    {
        if (empty($this->_aPages)) {
            $this->fetchPages();
        }
        return $this->_aPages;
    }

    /**
     * Get revision pages structure from DB
     * @return AM_Model_Db_Revision
     */
    public function fetchPages()
    {
        $oPages = AM_Model_Db_Table_Abstract::factory('page')->findAllBy(array('revision' => $this->id, 'deleted' => 'no'));

        //TODO: Is RowSet can return as $id=>$object ?
        $this->_aPages = array();
        foreach ($oPages as $oPage) {
            //Create array { page.id => page }
            $this->_aPages[$oPage->id] = $oPage;
        }

        //TODO: refactor
        $aTermsById = array();
        $oVocabularyToc = $this->getApplication()->getVocabularyToc();
        $oTerms = $oVocabularyToc->getToc();
        foreach ($oTerms as $oTerm) {
            //Create array { term.id => term }
            $aTermsById[$oTerm->id] = $oTerm;
        }

        $oVocabularyTag = $this->getApplication()->getVocabularyTag();
        $oTerms = $oVocabularyTag->getTags();
        foreach ($oTerms as $oTerm) {
            //Create array { term.id => term }
            $aTermsById[$oTerm->id] = $oTerm;
        }

        //Building pages tree
        foreach ($this->_aPages as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $sLinkType = $oPage->getLinkType();
            if (!empty($oPage->iParentId) && !empty($sLinkType)) {
                if (array_key_exists($oPage->iParentId, $this->_aPages)) {
                    $oParentPage = $this->_aPages[$oPage->iParentId];
                    /* @var $oParentPage AM_Model_Db_Page */
                    $oParentPage->addChild($oPage);
                }
            }
            //Set all page terms
            //Using terms that alrady fetched, because we will change them
            //and page will know about this changes
            $oTermPageSet = AM_Model_Db_Table_Abstract::factory('term_page')->findAllBy(array('page' => $oPage->id));
            if (!empty($oTermPageSet)) {
                foreach ($oTermPageSet as $oTermPage) {
                    if (array_key_exists($oTermPage->term, $aTermsById)) {
                        $oTerm = $aTermsById[$oTermPage->term];
                        $oPage->addTerm($oTerm);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Set application
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Revision
     */
    public function setApplication(AM_Model_Db_Application $oApplication)
    {
        $this->_oApplication = $oApplication;

        return $this;
    }

    /**
     * Get application model
     * @return AM_Model_Db_Application
     */
    public function getApplication()
    {
        if (empty($this->_oApplication)) {
            $this->fetchApplication();
        }

        return $this->_oApplication;
    }

    /**
     * Fetch application from DB
     * @return AM_Model_Db_Revision
     */
    public function fetchApplication()
    {
        $this->_oApplication  = $this->getIssue()->getApplication();

        if (empty($this->_oApplication)) {
            throw new AM_Model_Db_Exception(sprintf('Revision "%s"has no aplication', $this->id));
        }

        return $this;
    }

    /**
     * Set revision issue
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Model_Db_Revision
     */
    public function setIssue(AM_Model_Db_Issue $oIssue)
    {
        $this->_oIssue = $oIssue;
        return $this;
    }

    /**
     * Get revision issue
     * @param AM_Model_Db_Issue $issue
     * @return AM_Model_Db_Revision
     */
    public function getIssue()
    {
        if (empty($this->_oIssue)) {
            $this->fetchIssue();
        }
        return $this->_oIssue;
    }

    /**
     * Fetch revision issue
     * @return AM_Model_Db_Revision
     */
    public function fetchIssue()
    {
        $this->_oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy(array('id' => $this->issue));

        if (empty($this->_oIssue)) {
            throw new AM_Model_Db_Exception(sprintf('Revision "%s" has no issue', $this->id));
        }

        return $this;
    }

    /**
     * Get TOC vocabulary
     * @return AM_Model_Db_Vocabulary
     */
    public function getVocabularyToc()
    {
        $oVocabulary = $this->getApplication()->getVocabularyToc();

        return $oVocabulary;
    }

    /**
     * Get Tag vocabulary
     * @return AM_Model_Db_Vocabulary
     */
    public function getVocabularyTag()
    {
        $oVocabulary = $this->getApplication()->getVocabularyTag();

        return $oVocabulary;
    }

    /**
     * Init export process for revision
     * @return AM_Model_Db_Revision
     */
    public function exportRevision()
    {
        $oExportHandler = AM_Handler_Locator::getInstance()->getHandler('export');
        /* @var $oExportHandler AM_Handler_Export */
        $oExportHandler->initExportProcess($this);

        return $this;
    }

    /**
     * Delete revision softly
     */
    public function delete()
    {
        $aPages = $this->getPages();

        foreach ($aPages as &$oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oPage->delete();
        }

        $this->deleted = 'yes';
        $this->save();
    }
}
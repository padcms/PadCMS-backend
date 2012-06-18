<?php
/**
 * @file
 * AM_Model_Db_Issue class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Issue model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Issue extends AM_Model_Db_Abstract
{
    const HORISONTAL_MODE_ISSUE  = 'issue';
    const HORISONTAL_MODE_PAGE   = 'page';
    const HORISONTAL_MODE_2PAGES = '2pages';
    const HORISONTAL_MODE_NONE   = 'none';

    const VERTICAL_MODE_SIMPLE   = 'simple'; //On this mode we create issue from single pdf, each pdf's page is a page of issue
    const VERTICAL_MODE_ENRICHED = 'enriched'; //On this mode we create issue manually page-by-page

    const ORIENTATION_VERTICAL   = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';

    const STATUS_WIP       = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_ARCHIVED  = 3;
    const STATUS_REVIEW    = 4;

    //The types of vertical mode
    public static $issueTypes = array(self::VERTICAL_MODE_SIMPLE, self::VERTICAL_MODE_ENRICHED);

    //The types of horizontal mode
    public static $horizontalPdfTypes = array(self::HORISONTAL_MODE_ISSUE, self::HORISONTAL_MODE_PAGE, self::HORISONTAL_MODE_2PAGES, self::HORISONTAL_MODE_NONE);

    /** @var AM_Model_Db_Application **/
    protected $_oApplication = null; /**< @type AM_Model_Db_Application */

    /** @var AM_Model_Db_Rowset_Revision **/
    protected $_oRevisions = null; /**< @type AM_Model_Db_Rowset_Revision */

    /** @var AM_Model_Db_Rowset_StaticPdf **/
    protected $_oHorizontalPdfs = null; /**< @type AM_Model_Db_Rowset_StaticPdf */

    /** @var AM_Model_Db_IssueSimplePdf **/
    protected $_oSimplePdf = null; /**< @type AM_Model_Db_IssueSimplePdf */

    /** @var AM_Model_Db_Rowset_IssueHelpPage **/
    protected $_oHelpPages = null; /**< @type AM_Model_Db_Rowset_IssueHelpPage */


    /**
     * Set issue's application
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Issue
     */
    public function setApplication(AM_Model_Db_Application $oApplication)
    {
        $this->_oApplication = $oApplication;

        return $this;
    }

    /**
     * Get issue's application
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
     * Fetch application
     * @return AM_Model_Db_Issue
     */
    public function fetchApplication()
    {
        $this->_oApplication  = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $this->application);

        if (empty($this->_oApplication)) {
            throw new AM_Model_Db_Exception(sprintf('Issue "%s" has no application', $this->id));
        }

        return $this;
    }

    /**
     * Set issue revisions
     * @param $oRevisions AM_Model_Db_RevisionSet
     * @return AM_Model_Db_Issue
     */
    public function setRevisions(AM_Model_Db_Rowset_Revision $oRevisions)
    {
        $this->_oRevisions = $oRevisions;

        return $this;
    }

    /**
     * Get issue's revisions
     * @return AM_Model_Db_Rowset_Revision
     */
    public function getRevisions()
    {
        if (empty($this->_oRevisions)) {
            $this->fetchRevisions();
        }

        return $this->_oRevisions;
    }

    /**
     * Fetch issue's revisions
     * @return AM_Model_Db_Issue
     */
    public function fetchRevisions()
    {
        $this->_oRevisions = AM_Model_Db_Table_Abstract::factory('revision')->findAllBy(array('issue' => $this->id));
        if (!count($this->_oRevisions)) {
            $this->_oRevisions->setApplication($this->getApplication())
                             ->setIssue($this);
        }

        return $this;
    }

    /**
     * Get all published revisions
     *
     * @return AM_Model_Db_Rowset_Revision
     */
    public function getPublishedRevisions()
    {
        $oRevisions = AM_Model_Db_Table_Abstract::factory('revision')
                ->findAllBy(array('issue' => $this->id, 'state' => AM_Model_Db_State::STATE_PUBLISHED));

        return $oRevisions;
    }

    /**
     * Get horizontal pdfs
     * @return AM_Model_Db_Rowset_StaticPdf
     */
    public function getHorizontalPdfs()
    {
        if (empty($this->_oHorizontalPdfs)) {
            $this->fetchHorizontalPdfs();
        }

        return $this->_oHorizontalPdfs;
    }

    /**
     * Fetch horizontal pdfs
     * @return AM_Model_Db_Issue
     */
    public function fetchHorizontalPdfs()
    {
        $this->_oHorizontalPdfs = AM_Model_Db_Table_Abstract::factory('static_pdf')->findAllBy(array('issue' => $this->id));

        return $this;
    }

    /**
     * Set horizontal pdfs
     * @param AM_Model_Db_Rowset_StaticPdf $oHorizontalPdfs
     * @return AM_Model_Db_Issue
     */
    public function setHorizontalPdfs(AM_Model_Db_Rowset_StaticPdf $oHorizontalPdfs)
    {
        $this->_oHorizontalPdfs = $oHorizontalPdfs;

        return $this;
    }

    /**
     * Get simple pdf
     * @return AM_Model_Db_IssueSimplePdf
     */
    public function getSimplePdf()
    {
        if (is_null($this->_oSimplePdf)) {
            $this->_oSimplePdf = AM_Model_Db_Table_Abstract::factory('issue_simple_pdf')->findOneBy('id_issue', $this->id);
        }

        return $this->_oSimplePdf;
    }

    /**
     * Get help pages
     * @return AM_Model_Db_Rowset_IssueHelpPage
     */
    public function getHelpPages()
    {
        if (is_null($this->_oHelpPages)) {
            $this->_oHelpPages = AM_Model_Db_Table_Abstract::factory('issue_help_page')
                    ->findAllBy(array('id_issue' => $this->id));
        }

        return $this->_oHelpPages;
    }


    /**
     * Move issue to other user
     * @param AM_Model_Db_User $oUser
     * @return AM_Model_Db_Issue
     */
    public function moveToUser(AM_Model_Db_User $oUser)
    {
        if ($this->user == $oUser->id) {
            return $this;
        }

        $oRevisions = $this->getRevisions();

        $this->user = $oUser->id;
        $this->save();

        if (!empty($oRevisions)) {
            $oRevisions->moveToIssue($this);
        }

        $this->exportRevisions();

        return $this;
    }

    /**
     * Copy issue to other user
     * @param AM_Model_Db_User $oUser
     * @return AM_Model_Db_Issue
     */
    public function copyToUser(AM_Model_Db_User $oUser)
    {
        $oRevisions      = $this->getRevisions();
        $oHorizontalPdfs = $this->getHorizontalPdfs();
        $oApplication    = $this->getApplication();

        $aData = array();
        $aData['application'] = $oApplication->id;
        $aData['user']        = $oUser->id;
        $aData['created']     = null;
        $aData['updated']     = null;
        $aData['product_id']  = null;

        $this->copy($aData);

        if (!empty($oRevisions)) {
            $oRevisions->copyToIssue($this, false);
        }

        if (!empty($oHorizontalPdfs)) {
            $oHorizontalPdfs->copyToIssue($this);
        }

        $this->compileHorizontalPdfs();
        $this->exportRevisions();

        return $this;
    }

    /**
     * Copy issue to user application
     * @param AM_Model_Db_User $oUser
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Issue
     */
    public function copyToUserApplication(AM_Model_Db_User $oUser, AM_Model_Db_Application $oApplication)
    {
        /* @var $oRevisions AM_Model_Db_Rowset_Revision */
        $oRevisions  = $this->getRevisions();
        /* @var $oHorizontalPdfs AM_Model_Db_Rowset_StaticPdf*/
        $oHorizontalPdfs = $this->getHorizontalPdfs();

        $aData                = array();
        $aData['application'] = $oApplication->id;
        $aData['user']        = $oUser->id;
        $aData['created']     = null;
        $aData['updated']     = null;

        $this->copy($aData);

        $this->setApplication($oApplication);

        if (!empty($oRevisions)) {
            $oRevisions->copyToIssue($this, false);
        }

        if (!empty($oHorizontalPdfs)) {
            $oHorizontalPdfs->copyToIssue($this);
        }

        $this->compileHorizontalPdfs();
        $this->exportRevisions();

        return $this;
    }

    /**
     * Move issue to user application
     * @param AM_Model_Db_User $oUser
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Issue
     */
    public function moveToUserApplication(AM_Model_Db_User $oUser, AM_Model_Db_Application $oApplication)
    {
        if ($this->user == $oUser->id && $this->application == $oApplication->id) {
            return $this;
        }

        /* @var $oRevisions AM_Model_Db_Table_RevisionSet */
        $oRevisions  = $this->getRevisions();

        $this->user        = $oUser->id;
        $this->application = $oApplication->id;
        $this->save();
        $this->setApplication($oApplication);

        if (!empty($oRevisions)) {
            $oRevisions->moveToIssue($this, false);
        }

        $this->exportRevisions();

        return $this;
    }

    /**
     * Upload horizontal pdf
     * @return AM_Model_Db_StaticPdf
     */
    public function uploadHorizontalPdf()
    {
        //On PDF_MODE_ISSUE and PDF_MODE_2PAGES modes you can upload only one PDF per issue
        if (in_array($this->static_pdf_mode, array(self::HORISONTAL_MODE_ISSUE, self::HORISONTAL_MODE_2PAGES))) {
            //Remove all static pdfs
            AM_Model_Db_Table_Abstract::factory('static_pdf')->deleteBy(array('issue' => $this->id));
            AM_Tools::clearContent(AM_Model_Db_StaticPdf_Data_Abstract::TYPE, $this->id);
            AM_Tools::clearResizerCache(AM_Model_Db_StaticPdf_Data_Abstract::TYPE, $this->id);
        }

        $oHorizontalPdf = new AM_Model_Db_StaticPdf();
        $oHorizontalPdf->setIssue($this);
        $oHorizontalPdf->save();
        $oHorizontalPdf->uploadResource();

        $this->compileHorizontalPdfs();

        return $oHorizontalPdf;
    }

    /**
     * Upload vertical pdf
     * @return AM_Model_Db_IssueSimplePdf
     */
    public function uploadSimplePdf()
    {
        $oVerticalPdf = AM_Model_Db_Table_Abstract::factory('issue_simple_pdf')->findOneBy('id_issue', $this->id);
        if (is_null($oVerticalPdf)) {
            $oVerticalPdf = new AM_Model_Db_IssueSimplePdf();
        }

        AM_Tools::clearContent(AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE, $this->id);
        AM_Tools::clearResizerCache(AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE, $this->id);

        $oVerticalPdf->setIssue($this);
        $oVerticalPdf->save();
        $oVerticalPdf->uploadResource();

        return $oVerticalPdf;
    }

    /**
     * Upload help page
     * @param string $sHelpPageType
     * @return AM_Model_Db_IssueHelpPage
     */
    public function uploadHelpPage($sHelpPageType)
    {
        $oHelpPage = AM_Model_Db_Table_Abstract::factory('issue_help_page')
                ->findOneBy(array('id_issue' => $this->id, 'type' => $sHelpPageType));
        if (is_null($oHelpPage)) {
            $oHelpPage = new AM_Model_Db_IssueHelpPage();
        }

        $oHelpPage->setType($sHelpPageType);
        $oHelpPage->setIssue($this);
        $oHelpPage->save();

        $oHelpPage->uploadResource($sHelpPageType);

        return $oHelpPage;
    }

    /**
     * Compile issue
     * @return AM_Model_Db_Issue
     */
    public function compileHorizontalPdfs()
    {
        $oHorizontalPdfHandler = AM_Handler_Locator::getInstance()->getHandler('horisontal_pdf');
        /* @var $oHorizontalPdfHandler AM_Handler_HorisontalPdf */
        $oHorizontalPdfHandler->setIssue($this);
        $oHorizontalPdfHandler->compile();

        $this->updated_static_pdf = new Zend_DB_Expr('NOW()');
        $this->updated            = new Zend_DB_Expr('NOW()');
        $this->save();

        return $this;
    }

    /**
     * Returns horizontal versions archive
     * @return string
     */
    public function getHorizontalPdfsArchive()
    {
        $oHorizontalPdfHandler = AM_Handler_Locator::getInstance()->getHandler('horisontal_pdf');
        /* @var $oHorizontalPdfHandler AM_Handler_HorisontalPdf */
        $oHorizontalPdfHandler->setIssue($this);
        $sFilePath = $oHorizontalPdfHandler->getArchive();

        return $sFilePath;
    }

    /**
     * Get list of compilled horizontal files
     * @param string|null $sName Name pattern ('*_thumb.*' return all thumbnails of static pdfs)
     * @return AM_Model_Db_Rowset_PageHorisontal
     */
    public function getHorizontalPages($sName = null)
    {
        $oHorizontalPages = AM_Model_Db_Table_Abstract::factory('page_horisontal')
                ->findAllBy(array('id_issue' => $this->id), null, array('weight'));

        return $oHorizontalPages;
    }

    /**
     * Init export processes foreach issue's revision
     * @return AM_Model_Db_Issue
     */
    public function exportRevisions()
    {
        $oRevisions = $this->getRevisions();

        foreach ($oRevisions as $oRevision) {
            $oRevision->exportRevision();
        }

        return $this;
    }

    /**
     * Delete issue softly
     */
    public function delete()
    {
        $this->getRevisions()->delete();
        $this->getHorizontalPdfs()->delete();
        $this->getHelpPages()->delete();

        $oSimplePdf = $this->getSimplePdf();
        /* @var $oSimplePdf AM_Model_Db_IssueSimplePdf */
        if (!is_null($oSimplePdf)) {
            $oSimplePdf->delete();
        }

        $this->deleted = 'yes';
        $this->save();
    }
}
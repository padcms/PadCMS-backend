<?php
/**
 * @file
 * AM_View_Helper_Editor_Page class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_View_Helper
 */
class AM_View_Helper_Editor_Page extends AM_View_Helper_Abstract
{
    /** @var AM_Model_Db_Page **/
    protected $_oPage;

    /**
     * @param AM_Model_Db_Page $oPage
     */
    public function __construct(AM_Model_Db_Page $oPage)
    {
        $this->_oPage = $oPage;
    }

    /**
     * Prepares data for view
     */
    public function show()
    {
        $aPageInfo                         = array();
        $aPageInfo['template_title']       = $this->_oPage->getTemplate()->title;
        $aPageInfo['template_description'] = $this->_oPage->getTemplate()->description;
        $aPageInfo['canDelete']            = $this->_oPage->canDelete();
        $aPageInfo['canChangeTemplate']    = ($this->_oPage->template == AM_Model_Db_Template::TPL_COVER_PAGE)? false : true;
        $aPageInfo['tocItem']              = $this->_oPage->toc;
        $aPageInfo['tocList']              = $this->_getTocList();
        $aPageInfo['tags']                 = $this->_getTags();

        $aPageInfo = array_merge($aPageInfo, $this->_oPage->toArray());

        if ($this->_oPage->template == AM_Model_Db_Template::TPL_SLIDESHOW_PAGE) {
            $aPageInfo['showPdfPage'] = false;
        } else {
            $aPageInfo['showPdfPage'] = true;
        }

        $sName = $this->getName();
        if (isset($this->oView->$sName)) {
            $aPageInfo = array_merge($aPageInfo, $this->oView->$sName);
        }

        $this->oView->$sName = $aPageInfo;
    }

    /**
     * Get formatted array of TOC
     * @return array
     */
    protected function _getTocList()
    {
        $aResult = AM_Model_Db_Table_Abstract::factory('term')->getTocAsList($this->_oPage->getRevision());

        return array('' => 'Nothing selected') + $aResult;
    }

    /**
     * Get page's tags
     * @return null | array
     */
    protected function _getTags()
    {
        $oTags = $this->_oPage->getTags();

        if (!count($oTags)) {
            return null;
        }

        $aResult = array();
        foreach ($oTags as $oTag) {
            $aResult[] = array(
                'id'    => $oTag->id,
                'title' => $oTag->title
            );
        }

        return $aResult;
    }
}
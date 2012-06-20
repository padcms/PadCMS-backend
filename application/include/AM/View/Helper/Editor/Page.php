<?php
/**
 * @file
 * AM_View_Helper_Editor_Page class definition.
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
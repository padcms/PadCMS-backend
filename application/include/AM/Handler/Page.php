<?php
/**
 * @file
 * AM_Handler_Page class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Page handler
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Page extends AM_Handler_Abstract
{
    /**
     * Creates new page
     * TODO: there is a bug in JS - when page has been created, jumpers are not updated and has wrong id's
     * @param AM_Model_Db_Page $oPageParent
     * @param AM_Model_Db_Template $oTemplate
     * @param string $sConnectionType
     * @param array $aUser
     * @param bool $bBetween
     * @return \AM_Model_Db_Page
     */
    public function addPage(AM_Model_Db_Page $oPageParent, AM_Model_Db_Template $oTemplate, $sConnectionType, $aUser, $bBetween = false)
    {
        $oPageConnectedToParent = null;

        if (empty($aUser)) {
            throw new AM_Handler_Exception('Wrong user was given');
        }

        if (!in_array($sConnectionType, AM_Model_Db_Page::$aLinkTypes)) {
            throw new AM_Handler_Exception('Wrong connection type was given');
        }

        if ($bBetween) {
            //We trying to insert new page between two pages. We have to get both pages
            $oPageConnectedToParent = $oPageParent;
            $oPageParent            = AM_Model_Db_Table_Abstract::factory('page')->findConnectedPage($oPageConnectedToParent, $sConnectionType);
            if (is_null($oPageParent)) {
                throw new AM_Handler_Exception('Can\'t find parent page');
            }

            $oPageParent->setReadOnly(false);
        }

        if (is_null($oPageParent)) {
            throw new AM_Handler_Exception('Wrong parent page was given');
        }

        $oPage = new AM_Model_Db_Page();
        $oPage->title    = $sConnectionType . ' connected to page ' . $oPageParent->id;
        $oPage->template = $oTemplate->id;
        $oPage->revision = $oPageParent->revision;
        $oPage->user     = $aUser['id'];
        $oPage->created  = new Zend_Db_Expr('NOW()');
        $oPage->updated  = new Zend_Db_Expr('NOW()');
        $oPage->setConnectionBit($oPage->reverseLinkType($sConnectionType));
        $oPage->save();

        $oPage->setLinkType($sConnectionType);
        $oPage->setParent($oPageParent);
        $oPage->savePageImposition();

        $oPageParent->setConnectionBit($sConnectionType);
        $oPageParent->save();

        if (!is_null($oPageConnectedToParent)) {
            //Remove old connections
            AM_Model_Db_Table_Abstract::factory('page_imposition')
                    ->deleteBy(array('is_linked_to' => $oPageConnectedToParent->id, 'link_type' => $sConnectionType));

            $oPageConnectedToParent->setLinkType($sConnectionType);
            $oPageConnectedToParent->setParent($oPage);
            $oPageConnectedToParent->savePageImposition();

            $oPage->setConnectionBit($sConnectionType);
            $oPage->save();
        }

        return $oPage;
    }

    /**
     * Get page's branch
     *
     * @param AM_Model_Db_Page $oPage
     * @param string $sLinkType
     * @return array
     */
    public function getBranch(AM_Model_Db_Page $oPage, $sLinkType)
    {
        $aBranch = array();
        $this->_getParentsBranch($oPage, $aBranch, $sLinkType);
        $this->_getChildsBranch($oPage, $aBranch, $sLinkType);

        return $aBranch;
    }

    /**
     * Get branch from parents
     *
     * @param AM_Model_Db_Page $oPage
     * @param array $aBranch
     * @param string $sLinkType
     * @return \AM_Handler_Page
     */
    private function _getParentsBranch(AM_Model_Db_Page $oPage, &$aBranch, $sLinkType)
    {
        $sParentLinkType = $oPage->getLinkType(); //If page has parrent, page connected to parent on oPage::getLinkType side
        if (is_null($oPage->getLinkType())) {
            return $this;
        }

        if ($sLinkType == $oPage->reverseLinkType($sParentLinkType)) {
            $oPageParent = $oPage->getParent();
            $aBranch[] = self::parsePage($oPageParent);
            $this->_getChildsBranch($oPageParent, $aBranch, $sLinkType);
            $this->_getParentsBranch($oPageParent, $aBranch, $sLinkType);
        }

        return $this;
    }

    /**
     * Get branch from childs
     *
     * @param AM_Model_Db_Page $oPage
     * @param array $aBranch
     * @param string $sLinkType
     * @return \AM_Handler_Page
     */
    private function _getChildsBranch(AM_Model_Db_Page $oPage, &$aBranch, $sLinkType)
    {
        $aPageChilds = $oPage->getChilds();

        foreach ($aPageChilds as $oPageChild) {
            if ($sLinkType == $oPageChild->getLinkType()) {
                $aBranch[] = self::parsePage($oPageChild);
                $this->_getChildsBranch($oPageChild, $aBranch, $sLinkType);
                $this->_getParentsBranch($oPageChild, $aBranch, $sLinkType);
            }
        }

        return $this;
    }

    /**
     * Returns an array with pages data for view
     *
     * @param AM_Model_Db_Page $oPage
     * @return array
     */
    public static function parsePage(AM_Model_Db_Page $oPage)
    {
        $aPage = $oPage->toArray();

        $aPage['tpl_title'] = $oPage->getTemplate()->description;

        $aPage['tpl']['has_left']   = $oPage->getTemplate()->has_left_connector;
        $aPage['tpl']['has_right']  = $oPage->getTemplate()->has_right_connector;
        $aPage['tpl']['has_top']    = $oPage->getTemplate()->has_top_connector;
        $aPage['tpl']['has_bottom'] = $oPage->getTemplate()->has_bottom_connector;

        $aPage['has_left']   = $oPage->hasConnection(AM_Model_Db_Page::LINK_LEFT);
        $aPage['has_right']  = $oPage->hasConnection(AM_Model_Db_Page::LINK_RIGHT);
        $aPage['has_top']    = $oPage->hasConnection(AM_Model_Db_Page::LINK_TOP);
        $aPage['has_bottom'] = $oPage->hasConnection(AM_Model_Db_Page::LINK_BOTTOM);

        $oPageRoot = $oPage->getParent();
        if (!is_null($oPageRoot)) {
            $sLinkType                     = $oPage->reverseLinkType($oPage->getLinkType());
            $aPage[$sLinkType]             = $oPageRoot->id;
            $aPage['jumper_' . $sLinkType] = $oPage->getLinkType(); //The direction of the arrow in the pages tree
        }

        foreach ($oPage->getChilds() as $oPageChild) {
            $sLinkType                     = $oPageChild->getLinkType();
            $aPage[$sLinkType]             = $oPageChild->id;
            $aPage['jumper_' . $sLinkType] = $sLinkType; //The direction of the arrow in the pages tree
        }

        $aPage['link_type'] = $oPage->getLinkType();

        $aPage['thumbnailUri'] = $oPage->getPageBackgroundUri();

        return $aPage;
    }
}
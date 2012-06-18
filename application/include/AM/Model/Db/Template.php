<?php
/**
 * @file
 * AM_Model_Db_Template class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Template model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Template extends AM_Model_Db_Abstract
{
    const TPL_BASIC_ARTICLE                           = 1;
    const TPL_FIXED_ILLUSTRATION_ARTICLE              = 2;
    const TPL_SIMPLE_PAGE                             = 3;
    const TPL_SCROLLING_PAGE_VERTICAL                 = 4;
    const TPL_SCROLLING_PAGE_HORIZONTAL               = 20;
    const TPL_SLIDERS_BASED_MINI_ARTICLES_HORIZONTAL  = 5;
    const TPL_SLIDESHOW                               = 6;
    const TPL_COVER_PAGE                              = 7;
    const TPL_SLIDERS_BASED_MINI_ARTICLES_VERTICAL    = 8;
    const TPL_ARTICLE_WITH_OVERLAY                    = 9;
    const TPL_FIXED_ILLUSTRATION_ARTICLE_TOUCHABLE    = 10;
    const TPL_INTERACTIVES_BULLETS                    = 11;
    const TPL_SLIDESHOW_PAGE                          = 12;
    const TPL_SLIDERS_BASED_MINI_ARTICLES_TOP         = 13;
    const TPL_HTML_PAGE                               = 14;
    const TPL_DRAG_AND_DROP_PAGE                      = 15;
    const TPL_FLASH_BULLET_INTERACTIVE                = 16;
    const TPL_POPUP                                   = 17;
    const TPL_HTML5                                   = 18;
    const TPL_GAMES                                   = 19;
    const TPL_3D                                      = 21;

    /**
     * Checks if connector with link type $linkType exists
     * @param string $sLinkType
     * @return boolean
     * @throws AM_Model_Db_Exception
     */
    public function hasConnector($sLinkType)
    {
        $sLinkType = strtolower($sLinkType);

        if (!in_array($sLinkType, AM_Model_Db_Page::$aLinkTypes)) {
            throw new AM_Model_Db_Exception(sprintf('Wrong link type given "%s"', $sLinkType));
        }

        $sProperty = 'has_' . $sLinkType . '_connector';

        return (bool) $this->$sProperty;
    }

    /**
     * Get template picture
     * @param string $sType
     * @return string
     */
    public function getPicture($sType = 'map', $sOrientation = 'vertical')
    {
        switch ($sType) {
            case 'map':
                return '/img/thumbnails/map-item-' . $sOrientation . '-' . $this->id . '.png';
                break;

            case 'enabled':
                return '/img/templates/enabled-' . $sOrientation . '-' . $this->id . '.png';
                break;

            case 'disabled':
                return '/img/templates/disabled-' . $sOrientation . '-' . $this->id . '.png';
                break;
        }
    }
}
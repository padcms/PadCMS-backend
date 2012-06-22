<?php
/**
 * @file
 * AM_Model_Db_Template class definition.
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
 * Template model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Template extends AM_Model_Db_Abstract
{
    const TPL_BASIC_ARTICLE                           = 1;
    const TPL_SIMPLE_PAGE                             = 3;
    const TPL_SCROLLING_PAGE_VERTICAL                 = 4;
    const TPL_SCROLLING_PAGE_HORIZONTAL               = 20;
    const TPL_SLIDERS_BASED_MINI_ARTICLES_HORIZONTAL  = 5;
    const TPL_SLIDESHOW                               = 6;
    const TPL_COVER_PAGE                              = 7;
    const TPL_SLIDERS_BASED_MINI_ARTICLES_VERTICAL    = 8;
    const TPL_FIXED_ILLUSTRATION_ARTICLE_TOUCHABLE    = 10;
    const TPL_INTERACTIVES_BULLETS                    = 11;
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
<?php
/**
 * @file
 * AM_Model_Db_FieldType class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Field type model class
 * Each page contains few layers (fields) with a certain type
 * @ingroup AM_Model
 */
class AM_Model_Db_FieldType extends AM_Model_Db_Abstract
{
    const TYPE_BODY           = 'body';
    const TYPE_GALLERY        = 'gallery';
    const TYPE_MINI_ARTICLE   = 'mini_article';
    const TYPE_VIDEO          = 'video';
    const TYPE_BACKGROUND     = 'background';
    const TYPE_SCROLLING_PANE = 'scrolling_pane';
    const TYPE_SLIDE          = 'slide';
    const TYPE_OVERLAY        = 'overlay';
    const TYPE_SOUND          = 'sound';
    const TYPE_HTML           = 'html';
    const TYPE_ADVERT         = 'advert';
    const TYPE_DRAG_AND_DROP  = 'drag_and_drop';
    const TYPE_POPUP          = 'popup';
    const TYPE_HTML5          = 'html5';
    const TYPE_GAMES          = 'games';
    const TYPE_3D             = '3d';
}
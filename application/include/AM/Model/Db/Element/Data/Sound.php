<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Sound class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @todo Rename
 * @ingroup AM_Model
 */
class AM_Model_Db_Element_Data_Sound extends AM_Model_Db_Element_Data_Resource
{
    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('mp3', 'mp4'));
}
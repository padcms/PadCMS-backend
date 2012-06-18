<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Background class definition.
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
class AM_Model_Db_Element_Data_Background extends AM_Model_Db_Element_Data_Resource
{
    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('pdf', 'jpg', 'jpeg', 'png', 'zip'));
}
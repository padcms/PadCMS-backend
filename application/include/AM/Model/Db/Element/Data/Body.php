<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Body class definition.
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
class AM_Model_Db_Element_Data_Body extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_TOP                    = 'top';
    const DATA_KEY_HAS_PHOTO_GALLERY_LINK = 'hasPhotoGalleryLink';
    const DATA_KEY_SHOW_TOP_LAYER         = 'showTopLayer';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('pdf', 'jpg', 'jpeg', 'png', 'zip'));

    /**
     * Check top value
     * @param int $iValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addTop($iValue)
    {
        $iValue = intval($iValue);

        if ($iValue < 0) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_TOP));
        }

        return $iValue;
    }

    /**
     * Check hasPhotoGalleryLink value
     * @param int $iValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addHasPhotoGalleryLink($iValue)
    {
        $iValue = intval($iValue);

        if ($iValue < 0) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_HAS_PHOTO_GALLERY_LINK));
        }

        return $iValue;
    }
}
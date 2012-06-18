<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Advert class definition.
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
class AM_Model_Db_Element_Data_Advert extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_ADVERT_DURATION = 'advert_duration';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('pdf', 'jpg', 'jpeg', 'png'));

    /**
     * Check advert duration value
     * @param int $iValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addAdvertDuration($iValue)
    {
        $iValue = intval($iValue);

        if ($iValue < 0) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_ADVERT_DURATION));
        }

        return $iValue;
    }
}
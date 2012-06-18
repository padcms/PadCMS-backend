<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Video class definition.
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
class AM_Model_Db_Element_Data_Video extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_STREAM = 'stream';
    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('mp4', 'm4v'));

    /**
     * Check stream value
     * @param string $sValue
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addStream($sValue)
    {
        $sValue = (string) $sValue;

        if (!Zend_Uri::check($sValue)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_STREAM));
        }

        //Remove all resources keys from element
        $this->delete(self::DATA_KEY_RESOURCE);

        return $sValue;
    }

    /**
     * @param string $sValue
     * @return string
     */
    protected function _addResource($sValue)
    {
        //Remove all stream keys from element
        $this->delete(self::DATA_KEY_STREAM);

        return $sValue;
    }
}
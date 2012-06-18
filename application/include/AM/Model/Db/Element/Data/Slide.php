<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Slide class definition.
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
class AM_Model_Db_Element_Data_Slide extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_VIDEO = 'video';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('jpg', 'jpeg', 'png', 'zip', 'pdf'),
        self::DATA_KEY_VIDEO              => array('mp4', 'm4v'));

    protected function _init()
    {
        parent::_init();

        $this->addAdditionalResourceKey(array(self::DATA_KEY_VIDEO));
    }

    /**
     * Create new element
     *
     * @param AM_Model_Db_Page $oPage
     * @param AM_Model_Db_Field $oField
     * @return AM_Model_Db_Element
     */
    public static function getElementForPageAndField(AM_Model_Db_Page $oPage, AM_Model_Db_Field $oField)
    {
        $iMaxWeight = AM_Model_Db_Table_Abstract::factory('element')->getMaxElementWeight($oPage, $oField);

        $oElement = new AM_Model_Db_Element();
        $oElement->setPage($oPage);
        $oElement->weight = (is_null($iMaxWeight)) ? 0 : ++$iMaxWeight;
        $oElement->page  = $oPage->id;
        $oElement->field = $oField->id;
        $oElement->save();

        return $oElement;
    }

    /**
     * Returns path to video for manifest
     *
     * @return string|false
     */
    protected function _getExportVideo()
    {
        $sValue = $this->_getResourcePathForExport(self::DATA_KEY_VIDEO);

        return $sValue;
    }
}
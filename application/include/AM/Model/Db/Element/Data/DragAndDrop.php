<?php
/**
 * @file
 * AM_Model_Db_Element_Data_DragAndDrop class definition.
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
class AM_Model_Db_Element_Data_DragAndDrop extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_TOP_AREA           = 'top_area';
    const DATA_KEY_VIDEO              = 'video';
    const DATA_KEY_THUMBNAIL          = 'thumbnail';
    const DATA_KEY_THUMBNAIL_SELECTED = 'thumbnail_selected';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('pdf', 'zip'),
        self::DATA_KEY_VIDEO              => array('mp4', 'm4v'),
        self::DATA_KEY_THUMBNAIL          => array('jpg', 'jpeg', 'gif', 'png', 'pdf'),
        self::DATA_KEY_THUMBNAIL_SELECTED => array('jpg', 'jpeg', 'gif', 'png', 'pdf'));

    protected function _init()
    {
        parent::_init();

        $this->addAdditionalResourceKey(array(self::DATA_KEY_VIDEO,
                                              self::DATA_KEY_THUMBNAIL,
                                              self::DATA_KEY_THUMBNAIL_SELECTED));
    }

    /**
     * Check top_area value
     * @param int $iValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addTopArea($iValue)
    {
        $iValue = intval($iValue);

        if ($iValue < 0) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_TOP_AREA));
        }

        return $iValue;
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
        $oElement->page   = $oPage->id;
        $oElement->field  = $oField->id;
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

    /**
     * Returns path to thumbnail for manifest
     *
     * @return string|false
     */
    protected function _getExportThumbnail()
    {
        $sValue = $this->_getResourcePathForExport(self::DATA_KEY_THUMBNAIL);

        return $sValue;
    }

    /**
     * Returns path to thumbnail selected for manifest
     *
     * @return string|false
     */
    protected function _getExportThumbnailSelected()
    {
        $sValue = $this->_getResourcePathForExport(self::DATA_KEY_THUMBNAIL_SELECTED);

        return $sValue;
    }
}
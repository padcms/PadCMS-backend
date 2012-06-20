<?php
/**
 * @file
 * AM_Model_Db_Element_Data_DragAndDrop class definition.
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
<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Gallery class definition.
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
class AM_Model_Db_Element_Data_Gallery extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_GALLERY_ID  = 'gallery_id';
    const DATA_KEY_ENABLE_ZOOM = 'zoom';
    const DATA_GIF_RESOURCE    = 'gif_resource';


    protected static $_aAllowedFileExtensions = array(
        self::DATA_KEY_RESOURCE => array('jpg', 'jpeg', 'pdf', 'png'),
        self::DATA_GIF_RESOURCE => array('gif')
    );

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
     * Check zoom value
     * @param int $iValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addZoom($iValue)
    {
        $iValue = intval($iValue);

        if ($iValue < 0) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_ENABLE_ZOOM));
        }

        if (0 < $iValue) {
            $this->enableZooming();
        }

        return $iValue;
    }

    public function getImageType($sKeyName = self::DATA_KEY_RESOURCE)
    {
        if ($this->_oElement->getField()->template == AM_Model_Db_Template::TPL_ANIMATED_GIFS) {
            return AM_Handler_Thumbnail::IMAGE_TYPE_GIF;
        }

        return AM_Handler_Thumbnail::IMAGE_TYPE_JPEG;
    }


    /**
     * Upload resource
     * @param string $sResourceKey resource name
     * @return AM_Model_Db_Element_Data_Resource
     * @throws AM_Model_Db_Element_Data_Exception
     */
    public function upload($sResourceKey = self::DATA_KEY_RESOURCE)
    {
        $oUploader = $this->getUploader();
        if (!$oUploader->isUploaded($sResourceKey)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('File with variable name "%s" not found', $sResourceKey));
        }

        if ($this->_oElement->getField()->template == AM_Model_Db_Template::TPL_ANIMATED_GIFS) {
            $allowedFileExtensions = self::getAllowedFileExtensions(self::DATA_GIF_RESOURCE);
        }
        else {
            $allowedFileExtensions = self::getAllowedFileExtensions($sResourceKey);
        }

        //Validate uploaded file
        $oUploader->addValidator('Size', true, array('min' => 20, 'max' => 209715200))
            ->addValidator('Count', true, 1)
            ->addValidator('Extension', true, $allowedFileExtensions);

        if (!$oUploader->isValid()) {
            throw new AM_Model_Db_Element_Data_Exception($oUploader->getMessagesAsString());
        }

        $sDataPath = $this->_getResourceDir();
        if (!AM_Tools_Standard::getInstance()->is_dir($sDataPath)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sDataPath, 0777, true)) {
                throw new AM_Model_Db_Element_Data_Exception(sprintf('I/O error while create dir "%s"', $sDataPath));
            }
        }

        $aFiles         = $oUploader->getFileInfo();
        $sGivenFileName = $aFiles[$sResourceKey]['name'];
        $aFileInfo      = pathinfo($sGivenFileName);
        $sFileExtension = $aFileInfo['extension'];
        $sFileNameNew    = $sResourceKey . "." . $sFileExtension;

        //Copy resource
        $sDestination = $sDataPath . DIRECTORY_SEPARATOR . $sFileNameNew;

        //Rename file and receive it
        $oUploader->addFilter('Rename', array('target' => $sDestination, 'overwrite' => true));

        $this->clearResources($sResourceKey);

        if (!$oUploader->receive($sResourceKey)) {
            throw new AM_Model_Db_Element_Data_Exception($oUploader->getMessagesAsString());
        }

        $this->addKeyValue($sResourceKey, $sGivenFileName);
        $this->addKeyValue(self::DATA_KEY_IMAGE_TYPE, $this->getImageType());

        $this->_postUpload($sDestination, $sResourceKey);


        return $this;
    }
}
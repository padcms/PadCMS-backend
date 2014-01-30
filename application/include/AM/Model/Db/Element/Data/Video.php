<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Video class definition.
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
class AM_Model_Db_Element_Data_Video extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_STREAM          = 'stream';
    const DATA_KEY_ENABLE_LOOP     = 'loop_';
    const DATA_KEY_DISABLE_UI      = 'disable_user_interaction';
    const DATA_KEY_RESOURCE_SOUND  = 'resource_sound';
    const DATA_KEY_RESOURCE_VIDEO  = 'resource_video';
    protected static $_aAllowedFileExtensions = array(
        self::DATA_KEY_RESOURCE       => array('mp4', 'm4v', 'mp3'),
        self::DATA_KEY_RESOURCE_VIDEO => array('mp4', 'm4v'),
        self::DATA_KEY_RESOURCE_SOUND => array('mp3', 'mp4', 'm4v'),
    );

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

        //Remove all resources elements for this field
        //$this->delete(self::DATA_KEY_RESOURCE);

        $oField = $this->getElement()->getField();
        $oElements = $this->getElement()->getPage()->getElementsByField($oField);

        foreach ($oElements as $oElement) {
            $oElementData = AM_Model_Db_Table_Abstract::factory('element_data')->findOneBy(
                array(
                     'id_element' => $oElement->id,
                     'key_name'   => 'resource',
                ));
            if ($oElement->id != $this->getElement()->id && !empty($oElementData)) {
                $oElement->delete();
            }
        }

        return $sValue;
    }

    /**
     * @param string $sValue
     * @return string
     */
    protected function _addResource($sValue)
    {
        //Remove all stream elements for this field
        //$this->delete(self::DATA_KEY_STREAM);

        $oField = $this->getElement()->getField();
        $oElements = $this->getElement()->getPage()->getElementsByField($oField);

        foreach ($oElements as $oElement) {
            $oElementData = AM_Model_Db_Table_Abstract::factory('element_data')->findOneBy(
                array(
                     'id_element' => $oElement->id,
                     'key_name'   => 'stream',
                ));
            if ($oElement->id != $this->getElement()->id && !empty($oElementData)) {
                $oElement->delete();
            }
        }

        return $sValue;
    }

    /**
     * Returns type of image for conversion
     * @return string
     */
    public function getImageType($sKeyName = self::DATA_KEY_RESOURCE)
    {
        $sResource = $this->getDataValue(self::DATA_KEY_RESOURCE, 'resource.mp4');

        $sExtension = pathinfo($sResource, PATHINFO_EXTENSION);

        return $sExtension;
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
}

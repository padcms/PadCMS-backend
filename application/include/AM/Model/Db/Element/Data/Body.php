<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Body class definition.
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
class AM_Model_Db_Element_Data_Body extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_TOP                    = 'top';
    const DATA_KEY_HAS_PHOTO_GALLERY_LINK = 'hasPhotoGalleryLink';
    const DATA_KEY_SHOW_GALLERY_ON_ROTATE     = 'showGalleryOnRotate';
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

    /**
     * Check showGalleryOnRotate value
     * @param int $iValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addShowGalleryOnRotate($iValue)
    {
        $iValue = intval($iValue);

        if ($iValue < 0) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_SHOW_GALLERY_ON_ROTATE));
        }

        return $iValue;
    }

    public function getImageType()
    {
        //checking if template has background layer
        $oPage            = $this->getElement()->getPage();
        $oFieldBackground = AM_Model_Db_Table_Abstract::factory('field')->findOneBy(array('template' => $oPage->template, 'name' => AM_Model_Db_FieldType::TYPE_BACKGROUND));
        //If template doesn't have background layer body is alone and we don't need to have transparancy - using jpg
        if (is_null($oFieldBackground)) {
            return AM_Handler_Thumbnail::IMAGE_TYPE_JPEG;
        }

//        $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy(array('page' => $oPage->id, 'field' => $oFieldBackground->id));
//        //If template has background layer but it's empty
//        if (is_null($oElement)) {
//            return AM_Handler_Thumbnail::IMAGE_TYPE_JPEG;
//        }

        return AM_Handler_Thumbnail::IMAGE_TYPE_PNG;
    }
}
<?php
/**
 * @file
 * AM_View_Helper_Field_Video class definition.
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
 * @ingroup AM_View_Helper
 */
class AM_View_Helper_Field_Video extends AM_View_Helper_Field
{
    public function show()
    {
        $aElements = $this->_oPage->getElementsByField($this->_oField);
        $bIsStream = false;
        if (count($aElements)) {
            $aElementsView = array();
            foreach ($aElements as $oElement) {
                /* @var $oElement AM_Model_Db_Element */
                $aElementView = array (
                    'id' => $oElement->id
                );

                $aExtraDataItem = array(
                    AM_Model_Db_Element_Data_Video::DATA_KEY_STREAM
                );

                foreach ($aExtraDataItem as $sItem) {
                    $aElementView[$sItem] = $oElement->getResources()->getDataValue($sItem);
                    if (!empty($aElementView[$sItem])) {
                        $bIsStream = true;
                    }
                }

                $aElementView['loop'] = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Video::DATA_KEY_ENABLE_LOOP . $oElement->getField()->name, 0);
                $aElementView['ui']   = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Video::DATA_KEY_DISABLE_UI, 0);

                $aResourceView = $this->_getResourceViewData($oElement);
                $aElementView  = array_merge($aElementView, $aResourceView);

                $aElementsView[] = $aElementView;
            }

            /* @var $oElement AM_Model_Db_Element */
            $aElementView = array('id' => $oElement->id);
        }

        $aFieldView = array();

        if (isset($aElementsView)) {
            $aFieldView['elements'] = $aElementsView;
        }
        $aFieldView['isStream'] = $bIsStream;

//        if (!isset($aElementView) || !isset($aElementView['fileName'])) {
//            $aFieldView['defaultImageUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $this->_sPageOrientation, 'element', null, null);
//        }

        if ($this->_oField->name == 'video') {
            $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_Video::getAllowedFileExtensions(AM_Model_Db_Element_Data_Video::DATA_KEY_RESOURCE_VIDEO));
        }
        else {
            $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_Video::getAllowedFileExtensions(AM_Model_Db_Element_Data_Video::DATA_KEY_RESOURCE_SOUND));
        }
        sort($aExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $aExtensions);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

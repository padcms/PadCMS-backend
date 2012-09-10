<?php
/**
 * @file
 * AM_View_Helper_Field_Html5 class definition.
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
class AM_View_Helper_Field_Html5 extends AM_View_Helper_Field
{
    public function show()
    {
        $aElements = $this->_oPage->getElementsByField($this->_oField);

        $aFieldView = array();
        if (count($aElements)) {
            $aElementView = array();
            $oElement     = $aElements[0];
            /* @var $oElement AM_Model_Db_Element */
            $sBody = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Html5::DATA_KEY_HTML5_BODY);

            $aElementView[AM_Model_Db_Element_Data_Html5::DATA_KEY_HTML5_BODY] = $sBody;

            if (!empty($sBody)) {
                /** Select and fill fields, that refers to selected body */
                foreach (AM_Model_Db_Element_Data_Html5::$aFieldList[$sBody] as $sFieldName) {
                    $sFieldValue = $oElement->getResources()->getDataValue($sFieldName);
                    if (AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE == $sFieldName && !empty($sFieldValue)) {
                        $aFileInfo      = pathinfo($sFieldValue);
                        $sFileName      = $aFileInfo['filename'];
                        $sFileExtension = $aFileInfo['extension'];

                        $aElementView['element']['fileName']      = $sFileName . '.' . $sFileExtension;
                        $aElementView['element']['fileNameShort'] = $this->getHelper('String')->cut($sFileName) . '.' . $sFileExtension;

                        $sResourceFileName = AM_Model_Db_Element_Data_Html5::DATA_KEY_RESOURCE . '.' . $sFileExtension;
                        $aElementView['element']['smallUri'] = AM_Tools::getIconForNonImageFile($sResourceFileName);
                    }

                    $aElementView[$sFieldName] = $sFieldValue;
                }
                $aElementView['element']['id'] = $oElement->id;
            }
        }

        if (isset($aElementView)) {
            $aFieldView = $aElementView;
        }

        $aFieldView['select_body'] = AM_Model_Db_Element_Data_Html5::$aBodyList;

        $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_Html5::getAllowedFileExtensions());
        sort($aExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $aExtensions);
        $aFieldView['defaultImageUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $this->_sPageOrientation, 'element', null, null);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

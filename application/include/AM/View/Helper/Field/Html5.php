<?php
/**
 * @file
 * AM_View_Helper_Field_Html5 class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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

            $aElementView[AM_Model_Db_Element_Data_Html5::DATA_KEY_HTML5_POSITION] = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Html5::DATA_KEY_HTML5_POSITION);
            $aElementView[AM_Model_Db_Element_Data_Html5::DATA_KEY_HTML5_BODY]     = $sBody;

            if (!empty($sBody)) {
                /** Select and fill fields, that refers to selected body */
                foreach (AM_Model_Db_Element_Data_Html5::$aFieldList[$sBody] as $sFieldName) {
                    $sFieldValue = $oElement->getResources()->getDataValue($sFieldName);
                    if (AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE == $sFieldName && !empty($sFieldValue)) {
                        $aFileInfo      = pathinfo($sFieldValue);
                        $sFileName      = $aFileInfo['filename'];
                        $sFileExtension = $aFileInfo['extension'];

                        $aElementView['fileName']      = $sFileName . '.' . $sFileExtension;
                        $aElementView['fileNameShort'] = $this->getHelper('String')->cut($sFileName) . '.' . $sFileExtension;

                        $sResourceFileName = AM_Model_Db_Element_Data_Html5::DATA_KEY_RESOURCE . '.' . $sFileExtension;
                        $aElementView['smallUri'] = AM_Tools::getIconForNonImageFile($sResourceFileName);
                    }

                    $aElementView[$sFieldName] = $sFieldValue;
                }
                $aElementView['id'] = $oElement->id;
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

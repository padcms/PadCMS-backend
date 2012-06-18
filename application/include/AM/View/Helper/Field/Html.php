<?php
/**
 * @file
 * AM_View_Helper_Field_Html class definition.
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
class AM_View_Helper_Field_Html extends AM_View_Helper_Field
{
    public function show()
    {
        $aElements = $this->_oPage->getElementsByField($this->_oField);

        if (count($aElements)) {
            $oElement     = $aElements[0];
            /* @var $oElement AM_Model_Db_Element */
            $aElementView = array('id' => $oElement->id);

            $aExtraDataItem = array(
                AM_Model_Db_Element_Data_Html::DATA_KEY_TEMPLATE_TYPE,
                AM_Model_Db_Element_Data_Html::DATA_KEY_URL
            );

            foreach ($aExtraDataItem as $sItem) {
                $aElementView[$sItem] = $oElement->getResources()->getDataValue($sItem);
            }

            $aResourceView = $this->_getResourceViewData($oElement);
            $aElementView  = array_merge($aElementView, $aResourceView);
        }

        $aFieldView = array();

        if (isset($aElementView)) {
            $aFieldView['element'] = $aElementView;
        }

        if (!isset($aElementView) || !isset($aElementView['fileName'])) {
            $aFieldView['defaultImageUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $this->_sPageOrientation, 'element', null, null);
        }

        $sExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_Html::getAllowedFileExtensions());
        sort($sExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $sExtensions);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

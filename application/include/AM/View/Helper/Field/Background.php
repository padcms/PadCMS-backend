<?php
/**
 * @file
 * AM_View_Helper_Field_Background class definition.
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
class AM_View_Helper_Field_Background extends AM_View_Helper_Field
{
    public function show()
    {
        $oElements = $this->_oPage->getElementsByField($this->_oField);

        if (count($oElements)) {
            $oElement     = $oElements[0];
            /* @var $oElement AM_Model_Db_Element */
            $aElementView = array('id' => $oElement->id);

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

        $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_Background::getAllowedFileExtensions());
        sort($aExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $aExtensions);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

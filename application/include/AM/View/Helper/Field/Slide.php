<?php
/**
 * @file
 * AM_View_Helper_Field_Slide class definition.
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
class AM_View_Helper_Field_Slide extends AM_View_Helper_Field
{
    public function show()
    {
        $aElements = $this->_oPage->getElementsByField($this->_oField);

        if (count($aElements)) {
            $aElementsView = array();
            foreach ($aElements as $oElement) {
                /* @var $oElement AM_Model_Db_Element */
                $aElementView = array (
                    'id' => $oElement->id
                );

                $sVideo = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Slide::DATA_KEY_VIDEO);
                if (false !== $sVideo) {
                    $aElementView[AM_Model_Db_Element_Data_Slide::DATA_KEY_VIDEO] = $sVideo;
                }

                $aResourceView = $this->_getResourceViewData($oElement);
                $aElementView  = array_merge($aElementView, $aResourceView);

                $aElementsView[] = $aElementView;
            }
        }

        $aFieldView = array();

        if (isset($aElementsView)) {
            $aFieldView['elements'] = $aElementsView;
        }

        $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_Slide::getAllowedFileExtensions());
        sort($aExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $aExtensions);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

<?php
/**
 * @file
 * AM_View_Helper_Field_MiniArticle class definition.
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
class AM_View_Helper_Field_MiniArticle extends AM_View_Helper_Field
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

                $sUniq = '?' . strtotime($oElement->updated);

                $sVideo = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_VIDEO);
                if (false !== $sVideo) {
                    $aElementView[AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_VIDEO] = $sVideo;
                }

                $sThumbnail = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL);
                if (false !== $sThumbnail) {
                    $sFileExtension = pathinfo($sThumbnail, PATHINFO_EXTENSION);

                    $aElementView['thumbnail']    = $sThumbnail;
                    $aElementView['thumbnailUri'] = AM_Tools::getImageUrl('none', 'element', $oElement->id,
                            AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL. '.' . $sFileExtension ). $sUniq;
                }

                $sThumbnailSelected = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL_SELECTED);
                if (false !== $sThumbnailSelected) {
                    $sFileExtension = pathinfo($sThumbnail, PATHINFO_EXTENSION);

                    $aElementView['thumbnailSelected']    = $sThumbnailSelected;
                    $aElementView['thumbnailSelectedUri'] = AM_Tools::getImageUrl('none', 'element', $oElement->id,
                            AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL_SELECTED . '.' . $sFileExtension) . $sUniq;
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

        $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_MiniArticle::getAllowedFileExtensions());
        sort($aExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $aExtensions);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

<?php
/**
 * @file
 * AM_View_Helper_Field_Gallery class definition.
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
class AM_View_Helper_Field_Gallery extends AM_View_Helper_Field
{
    public function show()
    {
        $aGalleries = array_fill_keys(array(1, 2, 3, 4, 5, 6, 7), array());

        $aElements = $this->_oPage->getElementsByField($this->_oField);

        if (count($aElements)) {
            foreach ($aElements as $oElement) {
                /* @var $oElement AM_Model_Db_Element */
                $iGalleryId = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Gallery::DATA_KEY_GALLERY_ID, 1);

                $aElementView = array (
                    'id'      => $oElement->id,
                    'gallery' => $iGalleryId
                );

                $aResourceView = $this->_getResourceViewData($oElement);
                $aElementView  = array_merge($aElementView, $aResourceView);

                $aGalleries[$iGalleryId][] = $aElementView;
            }
        }

        $aFieldView = array();

        $aFieldView['galleries'] = $aGalleries;

        $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_Gallery::getAllowedFileExtensions());
        sort($aExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $aExtensions);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

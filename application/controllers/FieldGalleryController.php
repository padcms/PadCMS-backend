<?php
/**
 * @file
 * FieldGalleryController class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class is responsible for editing elements with type AM_Model_Db_FieldType::TYPE_GALLERY
 *
 * @ingroup AM_Controller_Action
 * @ingroup AM_Page_Editor
 */
class FieldGalleryController extends AM_Controller_Action_Field
{
    /**
     * Post upload trigger
     * @param AM_Model_Db_Element $oElement
     */
    protected function _postUpload(AM_Model_Db_Element $oElement)
    {
        $iGalleryId = intval($this->_getParam('gallery_id'));

        if (empty($iGalleryId)) $iGalleryId = 1;

        $oElement->getResources()->addKeyValue(AM_Model_Db_Element_Data_Gallery::DATA_KEY_GALLERY_ID, $iGalleryId);
    }
}
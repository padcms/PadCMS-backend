<?php
/**
 * @file
 * FieldDragAndDropController class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class is responsible for editing elements with type AM_Model_Db_FieldType::TYPE_DRAG_AND_DROP
 *
 * @ingroup AM_Controller_Action
 * @ingroup AM_Page_Editor
 */
class FieldDragAndDropController extends AM_Controller_Action_Field
{
    /**
     * Save action
     */
    public function saveAction()
    {
        try {
            $aMessage = array('status' => 0);

            $sKey   = trim($this->_getParam('key'));
            $sValue = trim($this->_getParam('value'));

            if (empty($sKey)) {
                throw new AM_Exception('Trying to set value with empty key');
            }

            $oField = AM_Model_Db_Table_Abstract::factory('field')->findOneBy('id', $this->_iFieldId);
            /* @var $oField AM_Model_Db_Field */
            if (is_null($oField)) {
                throw new AM_Exception(sprintf('Field with id "%d" not found.', $this->_iFieldId));
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $this->_iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Exception(sprintf('Page with id "%d" not found.', $this->_iPageId));
            }

            $iTopAreaId = intval($this->_getParam('topAreaId'));
            if ($iTopAreaId) {
                $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy(array('id' => $iTopAreaId));
            } else {
                $oElement = $oPage->getElementForField($oField);
            }

            /* @var $oElement AM_Model_Db_Element */
            $oElement->getResources()->addKeyValue($sKey, $sValue);

            $oPage->setUpdated(false);
            $aMessage['defaultImageUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $oPage->getOrientation(), 'element', null, '');
            $aMessage['status']          = 1;
        } catch (Exception $oException) {
            $aMessage["message"] = $this->localizer->translate("Error. Can't set value! ")
                    . $this->localizer->translate($oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage, false);
    }
}
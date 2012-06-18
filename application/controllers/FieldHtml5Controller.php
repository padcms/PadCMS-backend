<?php
/**
 * @file
 * FieldHtml5Controller class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class is responsible for editing elements with type AM_Model_Db_FieldType::TYPE_HTML5
 *
 * @ingroup AM_Controller_Action
 * @ingroup AM_Page_Editor
 */
class FieldHtml5Controller extends AM_Controller_Action_Field
{
    /**
     * Save action
     */
    public function saveAction()
    {
        try {
            $aMessage = array('status' => 0);

            $sBody = $this->_getParam('html5_body');

            if (empty($sBody)) {
                throw new AM_Controller_Exception_BadRequest('Body parameter not found');
            }

            $oField = AM_Model_Db_Table_Abstract::factory('field')->findOneBy('id', $this->_iFieldId);
            /* @var $oField AM_Model_Db_Field */
            if (is_null($oField)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Field with id "%d" not found.', $this->_iFieldId));
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $this->_iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Page with id "%d" not found.', $this->_iPageId));
            }

            $aBodyFields = array();
            foreach (AM_Model_Db_Element_Data_Html5::$aFieldList[$sBody] as $sFieldName) {
                if ($sFieldName == AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE) {
                    continue;
                }

                $sFieldValue = $this->_getParam($sFieldName);
                if (empty($sFieldValue)) {
                    throw new AM_Controller_Exception_BadRequest(sprintf('Error. parameter "%s" can not be empty', $sFieldName));
                }

                $aBodyFields[$sFieldName] = $sFieldValue;
            }

            if ($this->_getParam('html5_position')) {
                $aBodyFields['html5_position'] = $this->_getParam('html5_position');
            }

            $oElement = $oPage->getElementForField($oField);
            /* @var $oElement AM_Model_Db_Element */

            $aBodyFields = array_merge(array('html5_body' => $sBody), $aBodyFields);
            foreach ($aBodyFields as $sKey => $sValue) {
                $oElement->getResources()->addKeyValue($sKey, $sValue);
            }

            $oPage->setUpdated(false);
            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = $this->localizer->translate("Error. Can't set value! ")
                    . $this->localizer->translate($oException->getMessage());
        }

        return $this->getHelper('Json')->sendJson($aMessage, false);
    }
}
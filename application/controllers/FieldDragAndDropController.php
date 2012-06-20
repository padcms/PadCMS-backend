<?php
/**
 * @file
 * FieldDragAndDropController class definition.
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
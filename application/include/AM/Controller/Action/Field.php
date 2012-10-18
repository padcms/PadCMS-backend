<?php
/**
 * @file
 * AM_Controller_Action_Field class definition.
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
 * @defgroup AM_Page_Editor
 */

/**
 * This class is responsible for editing page's elements
 *
 * @ingroup AM_Controller_Action
 * @ingroup AM_Page_Editor
 */
class AM_Controller_Action_Field extends AM_Controller_Action
{
    /** @var int **/
    protected $_iPageId    = null; /**< @type int **/
    /** @var int **/
    protected $_iFieldId   = null; /**< @type int **/
    /** @var int **/
    protected $_iElementId = null; /**< @type int **/

    public function preDispatch()
    {
        parent::preDispatch();

        $this->_iPageId    = intval($this->_getParam('page_id'));
        $this->_iFieldId   = intval($this->_getParam('field_id'));
        $this->_iElementId = intval($this->_getParam('element'));

        //Checking permission to the page
        if ($this->_iPageId) {
            if (!AM_Model_Db_Table_Abstract::factory('page')->checkAccess($this->_iPageId, $this->_aUserInfo)) {
                $aMessage = array('state' => 1,
                    'message' => $this->__('Error. Access denied.'));

                return $this->getHelper('Json')->sendJson($aMessage, false);
            }
        }
        //Checking permission to the element
        if ($this->_iElementId) {
            if (!AM_Model_Db_Table_Abstract::factory('element')->checkAccess($this->_iElementId, $this->_aUserInfo)) {
                $aMessage = array('state' => 1,
                    'message' => $this->__('Error. Access denied.'));

                return $this->getHelper('Json')->sendJson($aMessage, false);
            }
        }
    }

    /**
     * Action saves key-value data for element
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

            if (!is_null($this->_iElementId)) {
                $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', $this->_iElementId);
                /* @var $oElement AM_Model_Db_Element */
                if (is_null($oElement)) {
                    $oElement = $oPage->getElementForField($oField);
                }
            } else {
                $oElement = $oPage->getElementForField($oField);
            }

            /* @var $oElement AM_Model_Db_Element */
            $oElement->getResources()->addKeyValue($sKey, $sValue);

            $oPage->setUpdated(false);
            $aMessage['defaultImageUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $oPage->getOrientation(), 'element', null, '');
            $aMessage['status']          = 1;
            $aMessage['value']           = $oElement->getResources()->getDataValue($sKey);
        } catch (Exception $oException) {
            $aMessage["message"] = $this->__('Error. Can\'t set value! ') . PHP_EOL . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage, false);
    }

    /**
     * Save element's weight
     */
    public function saveWeightAction()
    {
        try {
            $aMessage = array('status' => 0);

            $aWeight = $this->_getParam('weight');

            if (empty($aWeight) || !is_array($aWeight)) {
                throw new AM_Exception('Invalid params');
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $this->_iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Exception(sprintf('Page with id "%d" not found.', $this->_iPageId));
            }

            AM_Model_Db_Table_Abstract::factory('element')->updateElementWeigh($aWeight, $this->_iPageId);

            $oPage->setUpdated(false);

            $aMessage['background'] = $oPage->getPageBackgroundUri();
            $aMessage['status']     = 1;
        } catch (Exception $oException) {
            $aMessage["message"] = $this->localizer->translate('Error. Can\'t change weight') . PHP_EOL . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage);
    }

    /**
     * Delete element data
     */
    public function deleteAction()
    {
        try {
            $aMessage = array('status' => 0);

            $sKey = $this->_getParam('key');

            $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', $this->_iElementId);
            /* @var $oElement AM_Model_Db_Element */
            if (is_null($oElement)) {
                throw new AM_Exception(sprintf('Element with id "%d" not found.', $this->_iElementId));
            }
            $oPage = $oElement->getPage();
            /* @var $oPage AM_Model_Db_Page */

            if (empty($sKey)) {
                $oElement->delete();
            } else {
                $oElement->deleteDataByKey($sKey);
            }

            $oPage->setUpdated();
            $aMessage['key']             = str_replace("_", "-", $sKey);
            $aMessage['defaultImageUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $oPage->getOrientation(), 'element', null, '');
            $aMessage['background']      = $oPage->getPageBackgroundUri();
            $aMessage['status']          = 1;
        } catch (Exception $oException) {
            $aMessage['message']      = $this->__('Error. Can\'t delete this item!') . PHP_EOL . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage, false);
    }

    /**
     * Upload extra resource (thumbnails, video)
     */
    public function uploadExtraAction()
    {
        try {
            $aMessage = array('status' => 0);

            $sKey = (string) $this->_getParam('key');
            if (empty($sKey)) {
                throw new AM_Exception('Trying to set value with empty key');
            }

            if (!$this->_iElementId) {
                throw new AM_Exception('Parameter "element" is empty');
            }

            $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', $this->_iElementId);
            /* @var $oElement AM_Model_Db_Element */
            if (is_null($oElement)) {
                throw new AM_Exception(sprintf('Element with id "%d" not found.', $this->_iElementId));
            }

            $oElement->uploadResource($sKey);

            $sResourceFile  = $oElement->getResources()->getDataValue($sKey);
            $aFileInfo      = pathinfo($sResourceFile);
            $sFileName      = $aFileInfo['filename'];
            $sFileExtension = $aFileInfo['extension'];

            $aMessage['fileName'] = $sFileName . '.' . $sFileExtension;

            if ($sKey != AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_VIDEO) {
                $sUniq               = '?' . strtotime($oElement->updated);
                $aMessage['fileUri'] = AM_Tools::getImageUrl('none', 'element', $oElement->id, $sKey . '.' . $sFileExtension) . $sUniq;
            } else {
                $aMessage['fileUri'] = '#';
            }

            $oElement->getPage()->setUpdated(false);

            $aMessage['status'] = 1;
          } catch (Exception $e) {
            $aMessage["message"]      = $this->__('Error. Can\'t upload file.') . PHP_EOL . $e->getMessage();
        }

        return $this->sendJsonAsPlainText($aMessage);
    }

    /**
     * Upload resource
     */
    public function uploadAction()
    {
        try {
            $aMessage = array('status' => 0);

            if (!$this->_iPageId || !$this->_iFieldId) {
                throw new AM_Exception('Invalid params');
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

            $oElement = $oPage->getElementForField($oField);
            /* @var $oElement AM_Model_Db_Element */
            $oElement->uploadResource();

            $this->_postUpload($oElement);

            $sResourceFile      = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE);
            $sResourceImageType = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Resource::DATA_KEY_IMAGE_TYPE);

            $aFileInfo      = pathinfo($sResourceFile);
            $sFileName      = $aFileInfo['filename'];
            $sFileExtension = empty($sResourceImageType) ? $aFileInfo['extension'] : $sResourceImageType;

            $aResourceFileViewInfo                  = array();
            $aResourceFileViewInfo['fileName']      = $sFileName . '.' . $aFileInfo['extension'];
            $aResourceFileViewInfo['fileNameShort'] = $this->getHelper('String')->cut($sFileName) . '.' . $aFileInfo['extension'];

            $sResourceFileName = AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE . '.' . $sFileExtension;
            if (AM_Tools::isAllowedImageExtension($sResourceFile)) {
                $sOrientation = $oPage->getOrientation();
                if (AM_Model_Db_Template::TPL_SCROLLING_PAGE_HORIZONTAL == $oPage->template) {
                    $sOrientation = AM_Model_Db_Issue::ORIENTATION_HORIZONTAL;
                }
                $sUniq                             = '?' . strtotime($oElement->updated);
                $aResourceFileViewInfo['smallUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $sOrientation, 'element', $oElement->id, $sResourceFileName) . $sUniq;
                $aResourceFileViewInfo['bigUri']   = AM_Tools::getImageUrl('none', 'element', $oElement->id, $sResourceFileName) . $sUniq;
            } else {
                $aResourceFileViewInfo['smallUri'] = AM_Tools::getIconForNonImageFile($sResourceFileName);
            }
            $oPage->setUpdated();

            $aMessage['fieldTypeTitle'] = $oField->getFieldType()->title;
            $aMessage['background']     = $oPage->getPageBackgroundUri();
            $aMessage['element']        = $oElement->id;
            $aMessage['file']           = $aResourceFileViewInfo;
            $aMessage['status']         = 1;
        } catch (Exception $e) {
            $aMessage["message"]      = $this->__('Error. Can\'t upload file') . PHP_EOL . $e->getMessage();
        }

        return $this->sendJsonAsPlainText($aMessage);
    }

    /**
     * Post upload trigger
     * @param AM_Model_Db_Element $oElement
     */
    protected function _postUpload(AM_Model_Db_Element $oElement)
    { }
}

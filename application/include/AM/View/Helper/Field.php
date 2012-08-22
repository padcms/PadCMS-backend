<?php
/**
 * @file
 * AM_View_Helper_Field class definition.
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
 * @ingroup AM_View_Helper
 */
class AM_View_Helper_Field extends AM_View_Helper_Abstract
{
    /** @var AM_Model_Db_Field **/
    protected $_oField = null; /**< @type AM_Model_Db_Field */
    /** @var AM_Model_Db_Page **/
    protected $_oPage = null; /**< @type AM_Model_Db_Page */
    /** @var string **/
    protected $_sPageOrientation = null; /**< @type string */

    /**
     * @param AM_Model_Db_Field $oField
     * @param AM_Model_Db_Page $oPage
     */
    public function __construct(AM_Model_Db_Field $oField, AM_Model_Db_Page $oPage)
    {
        $this->_oField           = $oField;
        $this->_oPage            = $oPage;
        $this->_sPageOrientation = $oPage->getOrientation();
    }

    /**
     * Prepares data for view
     */
    public function show()
    {
        $aFieldView = array(
            'name'           => $this->getName(),
            'fieldId'        => $this->_oField->id,
            'fieldTypeTitle' => $this->_oField->getFieldType()->title,
            'pageId'         => $this->_oPage->id
        );

        $this->_setFieldData($aFieldView);
    }

    /**
     * Get helper name
     * @return string
     */
    public function getName()
    {
        if (is_null($this->_sName)) {
            $this->_sName = $this->_oField->getFieldType()->title;
        }

        return $this->_sName;
    }

    /**
     * Prepares vew data for resource
     * @param AM_Model_Db_Element $oElement
     * @param string $sResourceKeyName
     * @return array
     */
    protected function _getResourceViewData(AM_Model_Db_Element $oElement, $sResourceKeyName = AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE)
    {
        $sFile        = $oElement->getResources()->getDataValue($sResourceKeyName);
        $aElementView = array();

        if ($sFile) {
            $aFileInfo      = pathinfo($sFile);
            $sFileName      = $aFileInfo['filename'];
            $sFileExtension = $oElement->getResources()->getImageType();

            $aElementView['fileName']      = $sFileName . '.' . $aFileInfo['extension'];
            $aElementView['fileNameShort'] = $this->getHelper('String')->cut($sFileName) . '.' . $aFileInfo['extension'];

            $sResourceFileName = $sResourceKeyName . '.' . $sFileExtension;
            if (AM_Tools::isAllowedImageExtension($sFile)) {
                $sUniq                    = '?' . strtotime($oElement->updated);
                $aElementView['smallUri'] = AM_Tools::getImageUrl($this->_getThumbnailPresetName(), 'element', $oElement->id, $sResourceFileName) . $sUniq;
                $aElementView['bigUri']   = AM_Tools::getImageUrl('none', 'element', $oElement->id, $sResourceFileName) . $sUniq;
            } else {
                $aElementView['smallUri'] = AM_Tools::getIconForNonImageFile($sResourceFileName);
            }
        }

        return $aElementView;
    }

    /**
     * Set field's data to view
     * @param array $aData
     * @return AM_View_Helper_Field
     */
    protected function _setFieldData($aData)
    {
        $sViewVariableName = 'field_' . $this->getName();
        if (isset($this->oView->{$sViewVariableName})) {
            $aData = array_merge($aData, $this->oView->{$sViewVariableName});
        }
        $this->oView->{$sViewVariableName} = $aData;

        return $this;
    }

    /**
     * Returns preset name for thumbnail
     * @return string
     */
    protected function _getThumbnailPresetName()
    {
        return AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $this->_sPageOrientation;
    }
}
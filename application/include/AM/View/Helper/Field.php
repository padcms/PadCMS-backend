<?php
/**
 * @file
 * AM_View_Helper_Field class definition.
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
            $sFileExtension = $aFileInfo['extension'];

            $aElementView['fileName']      = $sFileName . '.' . $sFileExtension;
            $aElementView['fileNameShort'] = $this->getHelper('String')->cut($sFileName) . '.' . $sFileExtension;

            $sResourceFileName = $sResourceKeyName . '.' . $sFileExtension;
            if (AM_Tools::isAllowedImageExtension($sResourceFileName)) {
                $sUniq                    = '?' . strtotime($oElement->updated);
                $aElementView['smallUri'] = AM_Tools::getImageUrl(AM_Handler_Thumbnail_Interface::PRESET_FIELD . '-' . $this->_sPageOrientation, 'element', $oElement->id, $sResourceFileName) . $sUniq;
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
}
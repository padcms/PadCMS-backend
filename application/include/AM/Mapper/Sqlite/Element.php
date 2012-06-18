<?php
/**
 * @file
 * AM_Mapper_Sqlite_Element class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Mapper
 */
class AM_Mapper_Sqlite_Element extends AM_Mapper_Sqlite_Abstract
{
    const ELEMENT_DATA_MAPPER_CLASS_PREFIX = 'AM_Mapper_Sqlite_ElementData_';

    /**
     * @return AM_Mapper_Sqlite_Element
     */
    protected function _unmapCustom()
    {
        $sContentText = null;

        $oElementDataSet = $this->getModel()->getResources();
        /* @var $oElementDataSet AM_Model_Db_Element_Data_Abstract */
        foreach ($oElementDataSet->getData() as $oElementData ) {
            /* @var $oElementData AM_Model_Db_Element_Data_Abstract */

            if (AM_Model_Db_Element_Data_Resource::PDF_INFO == $oElementData->key_name) {
                if (!empty($oElementData->value)) {
                    $aPdfInfo     = json_decode($oElementData->value, true);
                    $sContentText = $aPdfInfo['text'];
                }
            }

            $sElementDataMapperClassName = self::ELEMENT_DATA_MAPPER_CLASS_PREFIX . Zend_Filter::filterStatic($oElementData->key_name, 'Word_UnderscoreToCamelCase');
            if (class_exists($sElementDataMapperClassName, true)) {
                $oElementDataMapper = new $sElementDataMapperClassName($oElementData, array('adapter' => $this->_getAdapter()));
                $oElementDataMapper->unmap();

                continue;
            }

            $aData = array(
                'element_id'  => $this->getModel()->id,
                'type'        => $oElementData->key_name,
                'value'       => $oElementDataSet->getDataValueForExport($oElementData->key_name),
                'position_id' => 0
            );
            $this->_getSqliteGateway('element_data')->insert($aData);
        }

        $aData = array(
            'id'                 => $this->getModel()->id,
            'page_id'            => $this->getModel()->page,
            'element_type_name'  => $this->getModel()->getFieldTypeTitle(),
            'weight'             => intval($this->getModel()->weight),
            'content_text'       => $sContentText
        );

        $this->_getSqliteGateway()->insert($aData);

        return $this;
    }
}
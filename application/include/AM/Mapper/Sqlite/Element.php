<?php
/**
 * @file
 * AM_Mapper_Sqlite_Element class definition.
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
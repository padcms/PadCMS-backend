<?php
/**
 * @file
 * AM_Mapper_Sqlite_ElementData_PdfInfo class definition.
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
 * @ingroup AM_Mapper
 */
class AM_Mapper_Sqlite_ElementData_ImageType extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_ElementData_PdfInfo
     */
    protected function _unmapCustom()
    {
        $oElementData = $this->getModel()->getPdfInfoElementData($this->getModel()->getElement()->getField()->id);
        if (!empty($oElementData)) {
            $aPdfInfo = Zend_Json_Decoder::decode($oElementData->value, true);
        }
        if (!empty($aPdfInfo['zones'])) {
            $this->_unmapImageType($aPdfInfo['zones']);
        }
        else {
            $this->_unmapImageType();
        }

        return $this;
    }

    /**
     * Parse pdf information
     *
     * @param array $aPdfInfo
     * @return AM_Mapper_Sqlite_ElementData_PdfInfo
     */
    private function _unmapImageType($aImageZones = null)
    {
        $iPositionId = 0;
        $zoneWeight = -1;
        $aFieldsWithDefinedZones = array(
            64 => 'gallery',
            66 => 'popup'
        );
        $iFieldId = $this->getModel()->getElement()->getField()->id;
        if (!empty($aImageZones) && array_key_exists($iFieldId, $aFieldsWithDefinedZones)) {
            foreach ($aImageZones as $aZone) {
                if (strpos($aZone['uri'], $aFieldsWithDefinedZones[$iFieldId]) !== FALSE) {
                    $zoneWeight = filter_var($aZone['uri'], FILTER_SANITIZE_NUMBER_INT);
                    if ($zoneWeight == $this->getModel()->getElement()->weight) {
                        break;
                    }
                }
            }
            if ($zoneWeight == $this->getModel()->getElement()->weight) {
                $aData = array(
                    'start_x' => $aZone['llx'],
                    'end_x'   => $aZone['urx'],
                    'start_y' => $aZone['lly'],
                    'end_y'   => $aZone['ury']
                );

                $iPositionId = $this->_getSqliteGateway('element_data_position')->insert($aData);
            }
        }

        $aData = array(
            'element_id'  => $this->getModel()->id_element,
            'type'        => $this->getModel()->key_name,
            'value'       => $this->getModel()->value,
            'position_id' => $iPositionId
        );
        $this->_getSqliteGateway('element_data')->insert($aData);

        return $this;
    }
}

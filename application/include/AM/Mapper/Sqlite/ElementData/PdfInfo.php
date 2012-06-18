<?php
/**
 * @file
 * AM_Mapper_Sqlite_ElementData_PdfInfo class definition.
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
class AM_Mapper_Sqlite_ElementData_PdfInfo extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_ElementData_PdfInfo
     */
    protected function _unmapCustom()
    {
        if (!empty($this->getModel()->value)) {
            $aPdfInfo     = json_decode($this->getModel()->value, true);
            $sContentText = $aPdfInfo['text'];
            $this->_unmapPdfInfo($aPdfInfo);
        }

        return $this;
    }

    /**
     * Parse pdf information
     *
     * @param array $aPdfInfo
     * @return AM_Mapper_Sqlite_ElementData_PdfInfo
     */
    private function _unmapPdfInfo($aPdfInfo)
    {
        //Parse page size
        if (array_key_exists('width', $aPdfInfo)) {
            $aData = array(
                'element_id'  => $this->getModel()->id_element,
                'type'        => 'width',
                'value'       => $aPdfInfo['width'],
                'position_id' => 0
            );
            $this->_getSqliteGateway('element_data')->insert($aData);
        }

        if (array_key_exists('height', $aPdfInfo)) {
            $aData = array(
                'element_id'  => $this->getModel()->id_element,
                'type'        => 'height',
                'value'       => $aPdfInfo['height'],
                'position_id' => 0
            );
            $this->_getSqliteGateway('element_data')->insert($aData);
        }

        if (array_key_exists('zones', $aPdfInfo)) {
            foreach ($aPdfInfo['zones'] as $aZone) {
                $aData = array(
                    'start_x' => $aZone['llx'],
                    'end_x'   => $aZone['urx'],
                    'start_y' => $aZone['lly'],
                    'end_y'   => $aZone['ury']
                );

                $iPositionId = $this->_getSqliteGateway('element_data_position')->insert($aData);

                $aData = array(
                    'element_id'  => $this->getModel()->id_element,
                    'type'        => 'active_zone',
                    'value'       => $aZone['uri'],
                    'position_id' => $iPositionId
                );
                $this->_getSqliteGateway('element_data')->insert($aData);
            }
        }

        return $this;
    }
}
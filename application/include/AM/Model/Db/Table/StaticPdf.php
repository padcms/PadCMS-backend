<?php
/**
 * @file
 * AM_Model_Db_Table_State class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Model
 * @todo rename
 */
class AM_Model_Db_Table_StaticPdf extends AM_Model_Db_Table_Abstract
{
    /**
     * Set weights for pdfs
     * @param array $aWeights Array of weights (id => weight)
     * @return AM_Model_Db_Table_StaticPdf
     */
    public function setWeight($aWeights)
    {
        $aWeights = (array) $aWeights;

        foreach ($aWeights as $iId => $iWeight) {
            $oHorizontalPdf = $this->findOneBy('id', intval($iId));
            if (!is_null($oHorizontalPdf)) {
                $oHorizontalPdf->weight = intval($iWeight);
                $oHorizontalPdf->save();
            }
        }

        return $this;
    }
}
<?php
/**
 * @file
 * AM_Model_Db_Table_Element class definition.
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
 * @ingroup AM_Model
 */
class AM_Model_Db_Table_Element extends AM_Model_Db_Table_Abstract
{

    /**
     * Get max weight of element for given page and field
     * @param AM_Model_Db_Page $oPage
     * @param AM_Model_Db_Field $oField
     * @return int | null
     */
    public function getMaxElementWeight(AM_Model_Db_Page $oPage, AM_Model_Db_Field $oField)
    {
        $iWeight = 0;

        $oQuery = $this->getAdapter()->select()
             ->from('element', array('max_weight' => 'MAX(element.weight)'))
             ->where('element.page = ?', $oPage->id)
             ->where('element.field = ?', $oField->id);

        $iWeight = $this->getAdapter()->fetchOne($oQuery);

        return $iWeight;
    }

    /**
     * Checks the client's access to the element
     * @param int $iElementId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iElementId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iElementId = intval($iElementId);
        $iClientId  = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                              ->from('element', array('element_id' => 'element.id'))

                              ->join('page', 'page.id = element.page', null)
                              ->join('revision', 'revision.id = page.revision', null)
                              ->join('issue', 'issue.id = revision.issue', null)
                              ->join('application', 'application.id = issue.application', null)
                              ->join('user', 'user.client = application.client', null)

                              ->where('page.deleted = ?', 'no')
                              ->where('revision.deleted = ?', 'no')
                              ->where('issue.deleted = ?', 'no')
                              ->where('application.deleted = ?', 'no')
                              ->where('user.deleted = ?', 'no')

                              ->where('element.id = ?', $iElementId)
                              ->where('user.client = application.client')
                              ->where('application.client = ?', $iClientId);

        $oElement = $this->getAdapter()->fetchOne($oQuery);
        $bResult  = $oElement ? true : false;

        return $bResult;
    }

    /**
     * Change elements weight
     * @param array $aWeights ['elementId' => 'elementWeight']
     * @param type $iPageId
     * @todo refactoring
     * @return void
     */
    public function updateElementWeigh($aWeights, $iPageId)
    {
        $sQuery = 'UPDATE element SET '
               . 'element.weight = '
               . 'CASE ';

        $aWhereInIds = array();
        foreach ($aWeights as $iKey => &$iValue) {
            $iKey          = intval($iKey);
            $iValue        = intval($iValue);
            $aWhereInIds[] = $iKey;
            $sQuery .= 'WHEN element.id = ' . $iKey . ' THEN ' . $iValue . ' ';
        }

        $sQuery .= 'ELSE element.weight ';
        $sQuery .= 'END '
                . 'WHERE '
                . 'element.page = ' . intval($iPageId) . ' '
                . 'AND element.id IN ' . '(' . implode(',', $aWhereInIds) . ')';

        $this->getAdapter()->query($sQuery);
    }
}
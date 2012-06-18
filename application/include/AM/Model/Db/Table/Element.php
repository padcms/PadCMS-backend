<?php
/**
 * @file
 * AM_Model_Db_Table_Element class definition.
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
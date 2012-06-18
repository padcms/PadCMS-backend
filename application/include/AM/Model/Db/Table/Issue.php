<?php
/**
 * @file
 * AM_Model_Db_Table_Issue class definition.
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
class AM_Model_Db_Table_Issue extends AM_Model_Db_Table_Abstract
{
    /**
     * Checks the client's access to the issue
     * @param int $iIssueId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iIssueId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iIssueId  = intval($iIssueId);
        $iClientId = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                              ->from('issue', array('issue_id' => 'issue.id'))

                              ->join('user', 'issue.user = user.id', null)
                              ->join('application', 'application.id = issue.application', null)

                              ->where('issue.deleted = ?', 'no')
                              ->where('user.deleted = ?', 'no')
                              ->where('application.deleted = ?', 'no')

                              ->where('issue.id = ?', $iIssueId)
                              ->where('user.client = application.client')
                              ->where('application.client = ?', $iClientId);

        $oIssue  = $this->getAdapter()->fetchOne($oQuery);
        $bResult = $oIssue ? true : false;

        return $bResult;
    }

    /**
     * Get issue by page id
     * @param int $iPageId
     * @return AM_Model_Db_Issue
     * @throws AM_Model_Db_Table_Exception
     */
    public function findOneByPageId($iPageId)
    {
        $iPageId = intval($iPageId);
        if ($iPageId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter PAGE_ID given');
        }

        $oQuery = $this->select()
                ->from('issue')
                ->join('revision', 'issue.id = revision.issue', null)
                ->join('page', 'revision.id = page.revision', null)
                ->where('page.id = ?', $iPageId);

        $oRow = $this->fetchRow($oQuery);

        return $oRow;
    }

    /**
     * Get all issues by application and user
     * @param int $iApplicationId
     * @param int $iUserId
     * @return AM_Model_Db_Rowset_Issue
     */
    public function findAllByApplicationIdAndUser($iApplicationId, $iUserId)
    {
        $iApplicationId = intval($iApplicationId);
        $iUserId        = intval($iUserId);
        if ($iApplicationId <= 0 || $iUserId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameters given');
        }

        $oSelect = $this->_findAllQuery()
                ->where('issue.application = ?', $iApplicationId)
                ->where('issue.user = ?', $iUserId);
        $oRows = $this->fetchAll($oSelect);

        return $oRows;
    }

    /**
     * Prepare query to find all issues
     * @return Zend_Db_Table_Select
     */
    protected function _findAllQuery()
    {
        $oQuery = $this->select()
                ->from('issue')
                ->where('issue.deleted = ?', 'no');

        return $oQuery;
    }
}
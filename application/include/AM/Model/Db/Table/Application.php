<?php
/**
 * @file
 * AM_Model_Db_Table_Application class definition.
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
class AM_Model_Db_Table_Application extends AM_Model_Db_Table_Abstract
{
    /**
     * Checks the client's access to the application
     * @param int $iApplicationId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iApplicationId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iApplicationId = intval($iApplicationId);
        $iClientId      = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                              ->from('application', array('application_id' => 'application.id'))

                              ->join('user', 'user.client = application.client', null)

                              ->where('application.deleted = ?', 'no')
                              ->where('user.deleted = ?', 'no')

                              ->where('application.id = ?', $iApplicationId)
                              ->where('application.client = ?', $iClientId)
                              ->where('user.client = application.client');

        $oApplication = $this->getAdapter()->fetchOne($oQuery);
        $bResult      = $oApplication ? true : false;

        return $bResult;
    }

    /**
     * Get all applications with issues by client id
     * @param int $iClientId
     * @return AM_Model_Db_Rowset_Application
     * @throws AM_Model_Db_Table_Exception
     */
    public function findAllWithIssuesByClientId($iClientId)
    {
        $iClientId = intval($iClientId);
        if ($iClientId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter CLIENT_ID given');
        }

        $oIssueSubquery = $this->_findWithIssueSubquery();

        $oQuery = $this->_findAllQuery()
                ->setIntegrityCheck(false)
                ->joinInner($oIssueSubquery, 'issue_application_id = application.id')
                ->where('application.client = ?', $iClientId);

        $oRows = $this->fetchAll($oQuery);

        return $oRows;
    }

    /**
     * Get all applications with issues by user id
     * @param int $iUserId
     * @return AM_Model_Db_Rowset_Application
     * @throws AM_Model_Db_Table_Exception
     */
    public function findAllWithIssuesByUserId($iUserId)
    {
        $iUserId = intval($iUserId);
        if ($iUserId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter USER_ID given');
        }

        $oIssueSubquery = $this->_findWithIssueSubquery();

        $oquery = $this->_findAllQuery()
                ->setIntegrityCheck(false)
                ->joinLeft('user', 'user.client = application.client', array('user_id' => 'user.id'))
                ->joinInner($oIssueSubquery, 'issue_user_id = user.id AND issue_application_id = application.id')
                ->where('user.id = ?', $iUserId);

        $oRows = $this->fetchAll($oquery);

        return $oRows;
    }

    /**
     * Prepare subquery to select applications with issues
     * @return Zend_Db_Table_Select
     */
    protected function _findWithIssueSubquery()
    {
        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('issue', array('issues_count' => new Zend_Db_Expr('COUNT(*)'), 'issue_application_id' => 'issue.application', 'issue_user_id' => 'issue.user' ))
                ->where('issue.deleted = ?', 'no')
                ->group('issue.application');

        return $oQuery;
    }

    /**
     * Get all applications by user id
     * @param int $iUserId
     * @return AM_Model_Db_Rowset_Application
     * @throws AM_Model_Db_Table_Exception
     */
    public function findAllByUsertId($iUserId)
    {
        $iUserId = intval($iUserId);
        if ($iUserId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter USER_ID given');
        }

        $oQuery = $this->_findAllQuery()
                ->setIntegrityCheck(false)
                ->joinLeft('user', 'user.client = application.client', null)
                ->where('user.id = ?', $iUserId);

        $oRows = $this->fetchAll($oQuery);

        return $oRows;
    }

    /**
     * Prepare query to find all applications
     * @return Zend_Db_Table_Select
     */
    public function _findAllQuery()
    {
        $oQuery = $this->select()
                ->from('application')
                ->where('application.deleted = ?', 'no');

        return $oQuery;
    }
}
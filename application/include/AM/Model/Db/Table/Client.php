<?php
/**
 * @file
 * AM_Model_Db_Table_Client class definition.
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
class AM_Model_Db_Table_Client extends AM_Model_Db_Table_Abstract
{
    /**
     * Get all clients with users, applications and issues
     * @return AM_Model_Db_Rowset_Client
     */
    public function findAllWithUsersAndAppsWithIssues()
    {
        $oIssueSubquery = $this->_findWithIssueSubquery();

        $oQuery = $this->_findWithUsersQuery()
                ->joinInner($oIssueSubquery, 'client.id = issue_user_client');

        $oRows = $this->fetchAll($oQuery);

        return $oRows;
    }

    /**
     * Get all clients with users and applications
     * @return AM_Model_Db_Rowset_Client
     */
    public function findAllWithUsersAndApps()
    {
        $oApplicationsSubquery = $this->_findWithApplicationSubquery();

        $oQuery = $this->_findWithUsersQuery()
                       ->joinInner($oApplicationsSubquery, 'client.id = app_client_id');

        $oRows = $this->fetchAll($oQuery);

        return $oRows;
    }

    /**
     * Get all clients with users
     * @return AM_Model_Db_Rowset_Client
     */
    public function findAllWithUsers()
    {
        $oQuery = $this->_findWithUsersQuery();
        $oRows  = $this->fetchAll($oQuery);

        return $oRows;
    }

    /**
     * Prepare subquery to select clients with issues
     * @return Zend_Db_Table_Select
     */
    protected function _findWithIssueSubquery()
    {
        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('issue', array('issues_count' => new Zend_Db_Expr('COUNT(*)')))
                ->joinLeft('user', 'user.id = issue.user', array('issue_user_client' => 'user.client'))
                ->where('issue.deleted = ?', 'no')
                ->group('issue.user');

        return $oQuery;
    }

    /**
     * Prepare subquery to select clients with apps
     * @return Zend_Db_Table_Select
     */
    protected function _findWithApplicationSubquery()
    {
        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('application', array('apps_count' => new Zend_Db_Expr('COUNT(*)'), 'app_client_id' => 'application.client'))
                ->where('application.deleted = ?', 'no')
                ->group('application.client');

        return $oQuery;
    }

    /**
     * Prepare query to select clients with users
     * @return Zend_Db_Table_Select
     */
    protected function _findWithUsersQuery()
    {
        $oUsersSubquery = $this->select()
                ->setIntegrityCheck(false)
                ->from('user', array('users_count' => new Zend_Db_Expr('COUNT(*)'), 'client_id' => 'user.client'))
                ->where('user.deleted = ?', 'no')
                ->group('user.client');

        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('client')
                ->where('client.deleted = ?', 'no')
                ->joinInner($oUsersSubquery, 'client.id = client_id');

        return $oQuery;
    }
}
<?php
/**
 * @file
 * AM_Model_Db_Table_Client class definition.
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
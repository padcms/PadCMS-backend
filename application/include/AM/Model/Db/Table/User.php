<?php
/**
 * @file
 * AM_Model_Db_Table_TermPage class definition.
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
class AM_Model_Db_Table_User extends AM_Model_Db_Table_Abstract
{
    /**
     * Checks the client's access to the user
     * @param int $iUserId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iUserId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iUserId   = intval($iUserId);
        $iClientId = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                              ->from('user')
                              ->join(array('u2' => 'user'), 'u2.client = user.client', null)

                              ->where('user.deleted = ?', 'no')
                              ->where('u2.deleted = ?', 'no')

                              ->where('user.id = ?', $iUserId)
                              ->where('u2.id = ?', $iClientId);

        $oApplication = $this->getAdapter()->fetchOne($oQuery);
        $bResult      = $oApplication ? true : false;

        return $bResult;
    }

    /**
     * Get all users with apps and issues by client id
     * @param int $iClientId
     * @return AM_Model_Db_Rowset_User
     */
    public function findAllWithIssuesByClientId($iClientId)
    {
        $iClientId = intval($iClientId);
        if ($iClientId <= 0) {
            throw new AM_Exception('Wrong parameter CLIENT_ID given');
        }

        $oIssueSubquery = $this->_findWithIssueSubquery();

        $oQuery = $this->_findAllQuery()
                    ->setIntegrityCheck(false)
                    ->joinInner($oIssueSubquery, 'issue_user_id = user.id')
                    ->where('user.client = ?', $iClientId);
        $oUsers = $this->fetchAll($oQuery);

        return $oUsers;
    }

    /**
     * Get all users with apps by client id
     * @param int $iClientId
     * @return AM_Model_Db_Rowset_User
     */
    public function findAllWithAppsByClientId($iClientId)
    {
        $iClientId = intval($iClientId);
        if ($iClientId <= 0) {
            throw new AM_Exception('Wrong parameter CLIENT_ID given');
        }

        $oAppsSubquery = $this->_findWithApplicationSubquery();

        $oQuery = $this->_findAllQuery()
                    ->setIntegrityCheck(false)
                    ->joinInner($oAppsSubquery, 'app_client_id = user.client')
                    ->where('user.client = ?', $iClientId);
        $oUsers = $this->fetchAll($oQuery);

        return $oUsers;
    }

    /**
     * Prepare subquery to select clients with issues
     * @return Zend_Db_Table_Select
     */
    protected function _findWithIssueSubquery()
    {
        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('issue', array('issues_count' => new Zend_Db_Expr('COUNT(*)'), 'issue_user_id' => 'issue.user'))
                ->where('issue.deleted = ?', 'no')
                ->group('issue.user');

        return $oQuery;
    }

    /**
     * Prepare subquery to select users with apps
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
     * Prepare query to find all users
     * @return Zend_Db_Table_Select
     */
    public function _findAllQuery()
    {
        $oQuery = $this->select()
                ->from('user')
                ->where('user.deleted = ?', 'no');

        return $oQuery;
    }

    /**
     * @see Zend_Db_Table_Abstract::fetchRow()
     */
//    public function fetchRow($where = null, $order = null)
//    {
//        if ($where instanceof Zend_Db_Table_Select) {
//            $where->setIntegrityCheck(false)
//                  ->joinLeft("client", "client.id = user.client", array("client_title" => "title"));
//        }
//
//        return parent::fetchRow($where, $order);
//    }
//
//    /**
//     * @see Zend_Db_Table_Abstract::fetchAll()
//     */
//    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
//    {
//        if ($where instanceof Zend_Db_Table_Select) {
//            $where->setIntegrityCheck(false)
//                  ->joinLeft("client", "client.id = user.client", array("client_title" => "title"));
//        }
//
//        return parent::fetchAll($where, $order, $count, $offset);
//    }
}
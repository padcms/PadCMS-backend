<?php
/**
 * @file
 * AM_Model_Db_Table_TermPage class definition.
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
<?php
/**
 * @file
 * AM_Model_Db_Table_Revision class definition.
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
class AM_Model_Db_Table_Revision extends AM_Model_Db_Table_Abstract
{

    /**
     * Checks the client's access to the revision
     * @param int $iRevisionId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iRevisionId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iRevisionId = intval($iRevisionId);
        $iClientId   = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                              ->from('revision', array('revision_id' => 'revision.id'))

                              ->join('issue', 'issue.id = revision.issue', null)
                              ->join('application', 'application.id = issue.application', null)
                              ->join('user', 'user.client = application.client', null)

                              ->where('revision.deleted = ?', 'no')
                              ->where('issue.deleted = ?', 'no')
                              ->where('application.deleted = ?', 'no')
                              ->where('user.deleted = ?', 'no')

                              ->where('revision.id = ?', $iRevisionId)
                              ->where('user.client = application.client')
                              ->where('application.client = ?', $iClientId);

        $oRevision = $this->getAdapter()->fetchOne($oQuery);
        $bResult   = $oRevision ? true : false;

        return $bResult;
    }

    /**
     * Move all published revisions to the archive state
     *
     * @param int $iIssueId
     * @return int The number of rows updated
     * @throws AM_Model_Db_Table_Exception
     */
    public function moveAllPublishedToArchive($iIssueId)
    {
        if (empty($iIssueId)) {
            throw new AM_Model_Db_Table_Exception('Incorrect parameters were given');
        }

        $aData = array(
            'state'   => AM_Model_Db_State::STATE_ARCHIVED,
            'updated' => new Zend_Db_Expr('NOW()')
        );

        $aWhere = array(
            $this->getAdapter()->quoteInto('issue = ?', $iIssueId),
            $this->getAdapter()->quoteInto('state = ?', AM_Model_Db_State::STATE_PUBLISHED),
        );

        $iResult = $this->update($aData, $aWhere);

        return $iResult;
    }
}
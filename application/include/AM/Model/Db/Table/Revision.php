<?php
/**
 * @file
 * AM_Model_Db_Table_Revision class definition.
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
<?php
/**
 * @file
 * AM_Model_Db_Rowset_Issue class definition.
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
class AM_Model_Db_Rowset_Issue extends AM_Model_Db_Rowset_Abstract
{
    /**
     * Move issues to other user
     * @param AM_Model_Db_User $oUser
     * @return AM_Model_Db_Rowset_Issue
     */
    public function moveToUser(AM_Model_Db_User $oUser)
    {
        foreach ($this as $oIssue) {
            $oIssue->moveToUser($oUser);
        }
        return $this;
    }

    /**
     * Copy issues to other user
     * @param AM_Model_Db_User $oUser
     * @return AM_Model_Db_Rowset_Issue
     */
    public function copyToUser(AM_Model_Db_User $oUser)
    {
        foreach ($this as $oIssue) {
            $oIssue->copyToUser($oUser);
        }
        return $this;
    }

    /**
     * Set application to issues
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Rowset_Issue
     */
    public function setApplication(AM_Model_Db_Application $oApplication)
    {
        foreach ($this as $oIssue) {
            $oIssue->setApplication($oApplication);
        }

        return $this;
    }

    /**
     * Init export processes foreach issues revisions
     * @return AM_Model_Db_Rowset_Issue
     */
    public function exportRevisions()
    {
        foreach ($this as $oIssue) {
            $oIssue->exportRevisions();
        }

        return $this;
    }
}
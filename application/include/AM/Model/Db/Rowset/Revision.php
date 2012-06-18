<?php
/**
 * @file
 * AM_Model_Db_Rowset_Revision class definition.
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
class AM_Model_Db_Rowset_Revision extends AM_Model_Db_Rowset_Abstract
{
    /**
     * Set application to revisions
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Rowset_Revision
     */
    public function setApplication(AM_Model_Db_Application $oApplication)
    {
        foreach ($this as $oRevision) {
            $oRevision->setApplication($oApplication);
        }

        return $this;
    }

    /**
     * Set revisions issue
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Model_Db_Rowset_Revision
     */
    public function setIssue(AM_Model_Db_Issue $oIssue)
    {
        foreach ($this as $oRevision) {
            $oRevision->setIssue($oIssue);
        }

        return $this;
    }

    /**
     * Copy revisions to other issue
     * @param AM_Model_Db_Issue $user
     * @return AM_Model_Db_Rowset_Revision
     */
    public function copyToIssue(AM_Model_Db_Issue $oIssue)
    {
        foreach ($this as $oRevision) {
            $oRevision->copyToIssue($oIssue);
        }
        return $this;
    }

    /**
     * Move revisions to other issue
     * @param AM_Model_Db_Issue $user
     * @return AM_Model_Db_Rowset_Revision
     */
    public function moveToIssue(AM_Model_Db_Issue $oIssue)
    {
        foreach ($this as $oRevision) {
            /* @var $oRevision AM_Model_Db_Revision */
            $oRevision->moveToIssue($oIssue);
        }
        return $this;
    }
}

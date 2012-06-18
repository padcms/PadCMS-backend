<?php
/**
 * @file
 * AM_Model_Db_Rowset_Page class definition.
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
class AM_Model_Db_Rowset_Page extends AM_Model_Db_Rowset_Abstract
{
    /**
     * Move a page from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Rowset_Page
     */
    public function moveToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        foreach ($this as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oPage->moveToRevision($oRevisionTo);
        }

        return $this;
    }

    /**
     * Copy a page from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Rowset_Page
     */
    public function copyToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        foreach ($this as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oPage->copyToRevision($oRevisionTo);
        }

        foreach ($this as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oPage->savePageImposition();
        }

        return $this;
    }
}

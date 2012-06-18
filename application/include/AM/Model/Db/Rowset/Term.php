<?php
/**
 * @file
 * AM_Model_Db_Rowset_Term class definition.
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
class AM_Model_Db_Rowset_Term extends AM_Model_Db_Rowset_Abstract
{
    /**
     * Copy a terms from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Rowset_Term
     */
    public function copyToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        //Inserting new records
        foreach ($this as $oTerm) {
            /* @var $oTerm AM_Model_Db_Term */
            $oTerm->copyToRevision($oRevisionTo);
        }

        //Update nesting reletions
        foreach ($this as $oTerm) {
            $oTerm->updateReletations();
        }

        return $this;
    }

    /**
     * Move a terms from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Rowset_Term
     */
    public function moveToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        //Inserting new records
        foreach ($this as $oTerm) {
            /* @var $oTerm AM_Model_Db_Term */
            $oTerm->moveToRevision($oRevisionTo);
        }

        return $this;
    }
}

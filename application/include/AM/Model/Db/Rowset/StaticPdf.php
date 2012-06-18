<?php
/**
 * @file
 * AM_Model_Db_Rowset_StaticPdf class definition.
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
class AM_Model_Db_Rowset_StaticPdf extends AM_Model_Db_Rowset_Abstract
{
    /**
     * Copy static pdfs to other issue
     * @param AM_Model_Db_Issue $user
     * @return AM_Model_Db_Rowset_StaticPdf
     */
    public function copyToIssue(AM_Model_Db_Issue $oIssue)
    {
        foreach ($this as $oHorizontalPdf) {
            /* @var $oHorizontalPdf AM_Model_Db_StaticPdf */
            $oHorizontalPdf->copyToIssue($oIssue);
        }
        return $this;
    }
}
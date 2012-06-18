<?php
/**
 * @file
 * AM_Model_Db_Rowset_Element class definition.
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
class AM_Model_Db_Rowset_Element extends AM_Model_Db_Rowset_Abstract
{
    /**
     * Copy element to other page
     * @param AM_Model_Db_Issue $user
     * @return AM_Model_Db_StaticPdfSet
     */
    public function copyToPage(AM_Model_Db_Page $oPage)
    {
        foreach ($this as $oElement) {
            /* @var $oElement AM_Model_Db_Element */
            $oElement->copyToPage($oPage);
        }
        return $this;
    }
}
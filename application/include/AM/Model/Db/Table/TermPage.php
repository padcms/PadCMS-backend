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
class AM_Model_Db_Table_TermPage extends AM_Model_Db_Table_Abstract
{
    /**
     * Get pages of term
     * @param int $iTermId Term id
     * @return AM_Model_Db_Rowset_Page
     * @throws AM_Model_Db_Table_Exception
     */
    public function findPagesByTermId($iTermId)
    {
        $iTermId = intval($iTermId);
        if ($iTermId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter TERM_ID given');
        }

        $oSelect = $this->select()
                ->setIntegrityCheck(false)
                ->from('page')
                ->joinLeft('term_page', 'term_page.page = page.id', null)
                ->where('term_page.term = ?', $iTermId)
                ->where('page.deleted = ?', 'no');
        $oPages = $this->fetchAll($oSelect);

        return $oPages;
    }
}
<?php
/**
 * @file
 * AM_Model_Db_Table_Field class definition.
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
class AM_Model_Db_Table_Field extends AM_Model_Db_Table_Abstract
{
    /**
     * Find all by page id
     * @param int $iPageId
     * @return AM_Model_Db_Rowset_Field
     */
    public function findAllByPageId($iPageId)
    {
        $iPageId = intval($iPageId);
        if ($iPageId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter "page_id" given');
        }

        $oSelect = $this->select()
                ->setIntegrityCheck(false)
                ->from('field')
                ->join('template', 'template.id = field.template', null)
                ->join('page', 'page.template = field.template', null)
                ->join('revision', 'revision.id = page.revision', null)
                ->join('issue', 'issue.id = revision.issue', null)
                ->join('application', 'application.id = issue.application', null)
                ->join('field', 'field.template = template.id', null)
                ->where('page.id = ?', $iPageId)
                ->where('application.version >= template.engine_version')
                ->group('field.id')
                ->order('field.weight ASC');

        $oRows = $this->fetchAll($oSelect);

        return $oRows;
    }
}
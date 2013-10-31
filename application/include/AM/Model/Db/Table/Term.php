<?php

/**
 * @file
 * AM_Model_Db_Table_Term class definition.
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
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Model
 */
class AM_Model_Db_Table_Term extends AM_Model_Db_Table_Abstract
{
    /**
     * Checks the client's access to the term
     * @param int $iTermId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iTermId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iTermId   = intval($iTermId);
        $iClientId = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                ->from('term', array('term_id' => 'term.id'))
                ->join('vocabulary', 'vocabulary.id = term.vocabulary', null)
                ->join('application', 'application.id = vocabulary.application', null)
                ->join('user', 'user.client = application.client', null)
                ->where('term.deleted = ?', 'no')
                ->where('application.deleted = ?', 'no')
                ->where('user.deleted = ?', 'no')
                ->where('term.id = ?', $iTermId)
                ->where('user.client = application.client')
                ->where('application.client = ?', $iClientId);

        $oTerm   = $this->getAdapter()->fetchOne($oQuery);
        $bResult = $oTerm ? true : false;

        return $bResult;
    }

    /**
     * Method returns formatted array of TOC
     * @param AM_Model_Db_Revision $oRevision
     * @return array
     * @todo Refactorng!!!!!!!
     */
    public function getTocAsList(AM_Model_Db_Revision $oRevision)
    {
        $aResult = $this->_getTree($oRevision);

        $aToc = array();
        $sGlue = ' / ';
        foreach ($aResult as $aRow) {
            if ($aRow['id3']) {
                $aToc[$aRow['id3']] = implode($sGlue, array($aRow['title1'], $aRow['title2'], $aRow['title3']));
            } else if ($aRow['id2']) {
                $aToc[$aRow['id2']] = implode($sGlue, array($aRow['title1'], $aRow['title2']));
            } else if ($aRow['id1']) {
                $aToc[$aRow['id1']] = implode($sGlue, array($aRow['title1']));
            }
        }

        return $aToc;
    }

    /**
     * Prepearing tree of the terms
     * @param AM_Model_Db_Revision $oRevision
     * @return array
     * @todo: refactoring! now it supports 3 levels only
     */
    public function getTocAsTree(AM_Model_Db_Revision $oRevision)
    {
        $aResult = $this->_getTree($oRevision);

        $aTree = array();

        foreach ($aResult as $aValue) {
            if ($aValue['id1']) {
                //Using type conversion to string to save correct ordering
                if (!array_key_exists((string) $aValue['id1'], $aTree)) {
                    $aTree[(string) $aValue['id1']] = array(
                        'parent_term' => null,
                        'attr'        => array('id' => $aValue['id1']),
                        'data'        => $aValue['title1'],
                        'children'    => array()
                    );
                }

                $aDataRoot = &$aTree[(string) $aValue['id1']];

                if ($aValue['id2']) {
                    if (!array_key_exists((string) $aValue['id2'], $aDataRoot['children'])) {
                        $aDataRoot['children'][(string) $aValue['id2']] = array(
                            'parent_term' => $aValue['id1'],
                            'attr'        => array('id' => $aValue['id2']),
                            'data'        => $aValue['title2'],
                            'children'    => array()
                        );
                    }

                    $aChild1 = &$aDataRoot['children'][(string) $aValue['id2']];

                    if ($aValue['id3']) {
                        if (!array_key_exists((string) $aValue['id3'], $aChild1['children'])) {
                            $aChild1['children'][(string) $aValue['id3']] = array(
                                'parent_term' => $aValue['id2'],
                                'attr'        => array('id' => $aValue['id3']),
                                'data'        => $aValue['title3'],
                                'children'    => array()
                            );
                        }
                    }
                }
            }
        }

        //Truncating keys of the tree array (jsTree requirements)
        $aResult = array();
        $clCallback = function(array $aElements, array $aResult) use(&$clCallback) {
                    foreach ($aElements as &$aElement) {
                        $aElement['children'] = $clCallback($aElement['children'], array());

                        $aResult[] = $aElement;
                    }

                    return $aResult;
                };

        $aTree = $clCallback($aTree, $aResult);

        return $aTree;
    }

    /**
     * Returns tree structure as array - each root element contains 2 childs
     * [
     *  [id1, id2, id3, title1, title2, title3]
     * ]
     *
     * @param AM_Model_Db_Revision $oRevision
     * @return array
     */
    protected function _getTree(AM_Model_Db_Revision $oRevision)
    {
        $oVocabulary = $oRevision->getVocabularyToc();
        $oQuery      = $this->getAdapter()
                ->select()
                ->from(array('t1' => 'term'), null)
                ->joinLeft(array('t2' => 'term'), 't2.parent_term = t1.id AND t2.deleted = "no"' . $this->getAdapter()->quoteInto(' AND (t2.revision = ? OR t2.revision IS NULL)', $oRevision->id), null)
                ->joinLeft(array('t3' => 'term'), 't3.parent_term = t2.id AND t3.deleted = "no"' . $this->getAdapter()->quoteInto(' AND (t3.revision = ? OR t3.revision IS NULL)', $oRevision->id), null)
                ->where('t1.vocabulary = ?', $oVocabulary->id)
                ->where('t1.revision = ? OR t1.revision IS NULL', $oRevision->id)
                ->where('t1.parent_term is null')
                ->where('t1.deleted = ?', 'no')
                ->order(array('t1.position', 't2.position', 't3.position', 't1.id', 't2.id', 't3.id'))
                ->columns(array(
            'id1'    => 't1.id',
            'id2'    => 't2.id',
            'id3'    => 't3.id',
            'title1' => 't1.title',
            'title2' => 't2.title',
            'title3' => 't3.title',
                ));

        $aResult = $this->getAdapter()->fetchAll($oQuery);

        return $aResult;
    }

    /**
     * Get selected term for page
     * @param AM_Model_Db_Page $oPage
     * @return AM_Model_Db_Term | null
     */
    public function getSelectedTOCItemForPage(AM_Model_Db_Page $oPage)
    {
        $oVocabulary = $oPage->getRevision()->getVocabularyToc();

        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('term')
                ->join('term_page', 'term_page.term = term.id', null)
                ->where('term.vocabulary = ?', $oVocabulary->id)
                ->where('term_page.page = ?', $oPage->id);

        $oTerm = $this->fetchRow($oQuery);

        return $oTerm;
    }

    /**
     * Remov all tocs from page
     *
     * @param AM_Model_Db_Page $oPage
     * @return integer The number of rows deleted.
     */
    public function removeTocFromPage(AM_Model_Db_Page $oPage)
    {
        $oVocabulary = $oPage->getIssue()->getApplication()->getVocabularyToc();

        $sQuery = 'DELETE term_page FROM term_page'
                . ' JOIN term on term.id = term_page.term'
                . ' WHERE '
                . $this->getAdapter()->quoteInto('term.vocabulary = ?', $oVocabulary->id)
                . ' AND '
                . $this->getAdapter()->quoteInto('term_page.page = ?', $oPage->id);

        $iResult = $this->getAdapter()->query($sQuery);

        return $iResult;
    }

    /**
     * Get tags for page
     * @param AM_Model_Db_Page $oPage
     * @return AM_Model_Db_Rowset_Term
     */
    public function getTagsForPage(AM_Model_Db_Page $oPage)
    {
        $oVocabulary = $oPage->getRevision()->getVocabularyTag();

        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('term')
                ->join('term_page', 'term_page.term = term.id', null)
                ->where('term.vocabulary = ?', $oVocabulary->id)
                ->where('term_page.page = ?', $oPage->id);

        $oTerms = $this->fetchAll($oQuery);

        return $oTerms;
    }

    /**
     * Retruns tags for autocomplete
     * @param AM_Model_Db_Page $oPage
     * @param string $sTagName
     * @return AM_Model_Db_Rowset_Term
     */
    public function getTagsForAutocomplete(AM_Model_Db_Page $oPage, $sTagName)
    {
        $oVocabulary = $oPage->getRevision()->getVocabularyTag();

        $oQuery = $this->select()
                ->from('term')
                ->setIntegrityCheck(false)
                ->joinLeft('page', $this->getAdapter()->quoteInto('page.id = ?', $oPage->id), null)
                ->joinLeft('term_page', 'term_page.page = page.id AND term_page.term = term.id', null)
                ->where('term.vocabulary = ?', $oVocabulary->id)
                ->where('term.deleted = "no"')
                ->where('term_page.id IS NULL')
                ->where('term.title LIKE CONCAT("%", ?, "%")', trim($sTagName));

        $oTerms = $this->fetchAll($oQuery);

        return $oTerms;
    }

    /**
     * Move the term to the given parent term, set position and change position of the terms on the same nesting level
     * @param AM_Model_Db_Term $oTerm
     * @param int $iTocTermParentId
     * @param type $iPosition
     */
    public function moveTerm(AM_Model_Db_Term $oTerm, $iTocTermParentId, $iPosition)
    {
        $iTocTermParentId = (0 === $iTocTermParentId) ? null : $iTocTermParentId;

        if ($oTerm->parent_term == $iTocTermParentId) {
            //We are moving term without the changing of parent term
            $aWhere = array('revision = ?' => $oTerm->revision);
            if ($oTerm->position < $iPosition) {
                //We are moving term down
                //Decrease the position of the term below the moving term
                $aWhere['position <= ?'] = $iPosition;
                $aWhere['position > ?']  = $oTerm->position;
                $sExpresion              = 'position - 1';
            } elseif ($oTerm->position > $iPosition) {
                //We are moving term up
                //Increase the position of the term below the new position of the moving term
                $aWhere['position >= ?'] = $iPosition;
                $aWhere['position < ?']  = $oTerm->position;
                $sExpresion              = 'position + 1';
            } else {
                return;
            }

            if (is_null($oTerm->parent_term)) {
                $aWhere['parent_term IS NULL'] = '';
            } else {
                $aWhere['parent_term = ?'] = $oTerm->parent_term;
            }
            $this->update(array('position' => new Zend_Db_Expr($sExpresion)), $aWhere);
        } else {
            //Moving term to the other parent term

            //Decreasing the position of the term below the moving term
            $aWhere = array('revision = ?' => $oTerm->revision);

            $aWhere['position > ?']  = $oTerm->position;
            $sExpresion              = 'position - 1';

            if (is_null($oTerm->parent_term)) {
                $aWhere['parent_term IS NULL'] = '';
            } else {
                $aWhere['parent_term = ?'] = $oTerm->parent_term;
            }

            $this->update(array('position' => new Zend_Db_Expr($sExpresion)), $aWhere);

            //Increase the position below the moved term in new parent term branch
            $aWhere = array('revision = ?' => $oTerm->revision);

            $aWhere['position >= ?'] = $iPosition;
            $sExpresion              = 'position + 1';

            if (is_null($iTocTermParentId)) {
                $aWhere['parent_term IS NULL'] = '';
            } else {
                $aWhere['parent_term = ?'] = $iTocTermParentId;
            }

            $this->update(array('position' => new Zend_Db_Expr($sExpresion)), $aWhere);
        }

        $oTerm->parent_term = $iTocTermParentId;
        $oTerm->position    = $iPosition;
        $oTerm->save();
    }

    /**
     * Retruns tags by title
     * @param AM_Model_Db_Page $oPage
     * @param string $sTagName
     * @return AM_Model_Db_Rowset_Term
     */
    public function getTagsByTitle($aTermTitle, $sEntityType, $sVocabularyId , $iEntityId = 0)
    {
        $oQuery = $this->select()
            ->from('term', array('MIN(term.id) as id', 'title'))
            ->setIntegrityCheck(false)
            ->joinLeft('term_entity', 'term_entity.entity = :entity AND term_entity.term = term.id AND term_entity.entity_type = :entity_type', 'id as term_id')
            ->where('term.deleted = "no"')
            ->where('term.title IN (' . $this->_db->quote($aTermTitle) . ')')
            ->where('term.vocabulary = :vocabulary_id')
            ->group('title')
            ->bind(
                array(
                     ':entity' => $iEntityId,
                     ':entity_type' => $sEntityType,
                     ':vocabulary_id' => $sVocabularyId
                ));

        $oTerms = $this->fetchAll($oQuery);

        return $oTerms;
    }

    /**
     * Retruns tags by title
     * @param AM_Model_Db_Page $oPage
     * @param string $sTagName
     * @return AM_Model_Db_Rowset_Term
     */
    public function getAutocompleteTags($sTermTitle, $iVocabularyId, $aExistingTags)
    {
        $oQuery = $this->select()
            ->from('term', array('id', 'title'))
            ->where('term.deleted = "no"')
            ->where('term.vocabulary = :vocabulary_id')
            ->where('term.title LIKE CONCAT("%", ?, "%")', trim($sTermTitle));
        if (!empty($aExistingTags)) {
            $oQuery->where('term.title NOT IN (' . $this->_db->quote($aExistingTags) . ')');
        }
        $oQuery->bind(
                array(
                     ':vocabulary_id' => $iVocabularyId
                ));

        $oTerms = $this->fetchAll($oQuery);

        return $oTerms;
    }

    public function getTagsForApplicationExisting($iVocabularyId)
    {
        $oQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from('term', array('id', 'title', 'term_entity.id AS te_id'))
            ->joinLeft('term_entity', 'term.id = term_entity.term', array())
            ->where('term.deleted = "no"')
            ->where('term.vocabulary = :vocabulary_id')
            ->where('term_entity.entity_type = "application"')
            ->order('delta ASC');
        $oQuery->bind(
            array(
                 ':vocabulary_id' => $iVocabularyId
            ));

        $oTerms = $this->fetchAll($oQuery);

        return $oTerms;
    }

    public function getTagsForApplicationPossible($iVocabularyId)
    {
        $oQuery = $this->select()
            ->setIntegrityCheck(false)
            ->distinct(true)
            ->from('term', array('id', 'title', 'te1.id AS te_id'))
            ->join('term_entity AS te1', 'term.id = te1.term AND te1.entity_type =  "issue"', array())
            ->joinLeft('term_entity AS te2', 'term.id = te2.term AND te2.entity_type = "application"', array())
            ->where('term.deleted = "no"')
            ->where('term.vocabulary = :vocabulary_id')
            ->where('te2.id IS NULL')
            ->group('title')
            ->order('title ASC');
        $oQuery->bind(
            array(
                 ':vocabulary_id' => $iVocabularyId
            ));

        $oTerms = $this->fetchAll($oQuery);

        return $oTerms;
    }

    public function updateTagsForApplication($aNewExistingTags, $iApplicationId)
    {
        $aOldExistingTags = array();
        if (empty($aNewExistingTags)) {
            $aNewExistingTags = array();
        }

        $oVocabulary = AM_Model_Db_Table_Abstract::factory('application')
            ->findOneBy('id', $iApplicationId)->getVocabularyTag();
        $oExistingTags = $this->getTagsForApplicationExisting($oVocabulary->id);
        $oTermEntityTable = AM_Model_Db_Table_Abstract::factory('term_entity');

        foreach ($oExistingTags as $oTag) {
            $aOldExistingTags[] = $oTag->te_id;
        }

        $aTagsForUpdate = array_intersect($aNewExistingTags, $aOldExistingTags);
        $aTagsForInsert = array_diff($aNewExistingTags, $aOldExistingTags);
        $aTagsForDelete = array_diff($aOldExistingTags, $aNewExistingTags);

        foreach ($aTagsForUpdate as $iDelta => $iTagEntityId) {
            $oTermEntityTable->update(array('delta' => $iDelta + 1), 'id = ' . (int) $iTagEntityId);
        }

        if (!empty($aTagsForInsert)) {
            $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('term_entity', array('id', 'term'))
                ->where('term_entity.id IN (' . $this->_db->quote($aTagsForInsert) . ')');

            $oTermEntityIds = $oTermEntityTable->fetchAll($oQuery);

            $aTagsIdForInsert = array();
            foreach ($oTermEntityIds as $oTermEntityId) {
                $aTagsIdForInsert[$oTermEntityId->id] = $oTermEntityId->term;
            }
            foreach ($aTagsForInsert as $iDelta => $iTagEntityId) {
                $oTermEntityTable->insert(array(
                   'term'        => $aTagsIdForInsert[$iTagEntityId],
                   'entity_type' => 'application',
                   'entity'      => $iApplicationId,
                   'delta'       => $iDelta + 1,
                ));
            }
        }

        if (!empty($aTagsForDelete)) {
            $oTermEntityTable->delete('id IN (' . $this->_db->quote($aTagsForDelete) . ')');
        }
    }
}
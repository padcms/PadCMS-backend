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
 * @author $DOXY_AUTHOR
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
        $oVocabulary = $oRevision->getVocabularyToc();
        $oQuery = $this->getAdapter()
                ->select()
                ->from(array('t1' => 'term'), null)
                ->joinLeft(array('t2' => 'term'), 't2.parent_term = t1.id AND t2.deleted = "no"' . $this->getAdapter()->quoteInto(' AND (t2.revision = ? OR t2.revision IS NULL)', $oRevision->id), null)
                ->joinLeft(array('t3' => 'term'), 't3.parent_term = t2.id AND t3.deleted = "no"' . $this->getAdapter()->quoteInto(' AND (t3.revision = ? OR t3.revision IS NULL)', $oRevision->id), null)
                ->joinLeft(array('t4' => 'term'), 't4.parent_term = t3.id AND t4.deleted = "no"' . $this->getAdapter()->quoteInto(' AND (t4.revision = ? OR t4.revision IS NULL)', $oRevision->id), null)
                ->joinLeft(array('t5' => 'term'), 't5.parent_term = t4.id AND t5.deleted = "no"' . $this->getAdapter()->quoteInto(' AND (t5.revision = ? OR t5.revision IS NULL)', $oRevision->id), null)
                ->where('t1.vocabulary = ?', $oVocabulary->id)
                ->where('t1.revision = ? OR t1.revision IS NULL', $oRevision->id)
                ->where('t1.parent_term is null')
                ->where('t1.deleted = ?', 'no')
                ->order(array('t1.id', 't2.id', 't3.id', 't4.id', 't5.id',))
                ->columns(array(
                    'id1' => 't1.id',
                    'id2' => 't2.id',
                    'id3' => 't3.id',
                    'id4' => 't4.id',
                    'id5' => 't5.id',
                    'title1' => 't1.title',
                    'title2' => 't2.title',
                    'title3' => 't3.title',
                    'title4' => 't4.title',
                    'title5' => 't5.title'
                ));

        $aResult = $this->getAdapter()->fetchAll($oQuery);

        $aToc = array();
        $sGlue = ' / ';
        foreach ($aResult as $row) {
            if ($row['id5']) {
                $aToc[$row['id5']] = implode($sGlue, array($row['title1'], $row['title2'], $row['title3'], $row['title4'], $row['title5']));
            } else if ($row['id4']) {
                $aToc[$row['id4']] = implode($sGlue, array($row['title1'], $row['title2'], $row['title3'], $row['title4']));
            } else if ($row['id3']) {
                $aToc[$row['id3']] = implode($sGlue, array($row['title1'], $row['title2'], $row['title3']));
            } else if ($row['id2']) {
                $aToc[$row['id2']] = implode($sGlue, array($row['title1'], $row['title2']));
            } else if ($row['id1']) {
                $aToc[$row['id1']] = implode($sGlue, array($row['title1']));
            }
        }

        return $aToc;
    }

    /**
     * Prepearing tree of the terms
     * @param AM_Model_Db_Revision $oRevision
     * @param boolean $bOnlyPermanent
     * @return array
     * @todo: refactoring! now it supports 3 levels only
     */
    public function getTocAsTree(AM_Model_Db_Revision $oRevision, $bOnlyPermanent = false)
    {
        $oVocabulary = $oRevision->getVocabularyToc();

        $oQuery = $this->select()
            ->from('term')

            ->where('term.vocabulary = ?', $oVocabulary->id)
            ->where('term.deleted = ?', 'no')

            ->order(array('term.id ASC', 'term.parent_term ASC'));

        if ($bOnlyPermanent) {
            $oQuery->where('term.revision IS NULL');
        } else {
            $oQuery->where('term.revision IS NULL OR term.revision = ?', $oRevision->id);
        }

        $oTerms     = $this->fetchAll($oQuery);
        $aTree      = array();
        $aTermsById = array();

        //Prepearing array with ids of terms, need to check correction of the parent term
        foreach ($oTerms as $oTerm) {
            $aTermsById[$oTerm->id] = $oTerm;
        }

        foreach ($oTerms as $oTerm) {
            $aAttributes = array('id' => $oTerm->id);

            if (is_null($oTerm->revision)) {
                $aAttributes['rel'] = 'permanent';
            }

            $aData = array(
                'parent_term' => $oTerm->parent_term,
                'attr'        => $aAttributes,
                'data'        => $oTerm->title,
                'children'    => array()
            );

            if (is_null($oTerm->parent_term)) {
                $aTree[$oTerm->id] = $aData;
            } else {
                //Checking if parent term id exists
                if (array_key_exists($oTerm->parent_term, $aTermsById)) {
                    $oParentTerm = $aTermsById[$oTerm->parent_term];
                    if (!is_null($oParentTerm->parent_term) && array_key_exists($oParentTerm->parent_term, $aTermsById)){
                        $aTree[$oParentTerm->parent_term]['children'][$oTerm->parent_term]['children'][$oTerm->id] = $aData;
                    } else {
                        $aTree[$oTerm->parent_term]['children'][$oTerm->id] = $aData;
                    }
                }
            }
        }

        $aResult = array();

        //Truncating keys of the tree array (jsTree requirements)
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
}
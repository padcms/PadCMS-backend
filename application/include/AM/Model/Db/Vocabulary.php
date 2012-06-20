<?php
/**
 * @file
 * AM_Model_Db_Vocabulary class definition.
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
 * Vocabulary model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Vocabulary extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_Rowset_Term **/
    protected $_oToc = null; /**< @type AM_Model_Db_Rowset_Term */
    /** @var AM_Model_Db_Rowset_Term **/
    protected $_oTags = null; /**< @type AM_Model_Db_Rowset_Term */

    /**
     * Set TOC
     * @param $oToc AM_Model_Db_Rowset_Term
     * @return AM_Model_Db_Vocabulary
     */
    public function setToc(AM_Model_Db_Rowset_Term $oToc)
    {
        $this->_oToc = $oToc;

        return $this;
    }

    /**
     * Get TOC
     * @return AM_Model_Db_Rowset_Term
     */
    public function getToc()
    {
        if (is_null($this->_oToc)) {
            $this->fetchToc();
        }

        return $this->_oToc;
    }

    /**
     * Fetch TOC
     * @return AM_Model_Db_Vocabulary
     */
    public function fetchToc()
    {
        $this->_oToc = AM_Model_Db_Table_Abstract::factory('term')->findAllBy(array('deleted' => 'no', 'vocabulary' => $this->id));

        //TODO: Is RowSet can return as $id=>$object ?
        $aTermsById = array();
        foreach ($this->_oToc as $oTerm) {
            //Create array { term.id => term }
            $aTermsById[$oTerm->id] = $oTerm;
            /* @var $oTerm AM_Model_Db_Term */
            $oTerm->setVocabulary($this);
        }

        foreach ($this->_oToc as $oTerm) {
            if (!empty($oTerm->parent_term)) {
                if (array_key_exists($oTerm->parent_term, $aTermsById)) {
                    $oParentTerm = $aTermsById[$oTerm->parent_term];
                    /* @var $oParentTerm AM_Model_Db_Term */
                    $oParentTerm->addChild($oTerm);
                }
            }
        }

        return $this;
    }

    /**
     * Set tags
     * @param $oTags AM_Model_Db_TermSet
     * @return AM_Model_Db_Vocabulary
     */
    public function setTags(AM_Model_Db_Rowset_Term $oTags)
    {
        $this->_oTags = $oTags;

        return $this;
    }

    /**
     * Get Tags
     * @return AM_Model_Db_Rowset_Term
     */
    public function getTags()
    {
        if (is_null($this->_oTags)) {
            $this->fetchTags();
        }

        return $this->_oTags;
    }

    /**
     * Fetch Tags
     * @return AM_Model_Db_Vocabulary
     */
    public function fetchTags()
    {
        $this->_oTags = AM_Model_Db_Table_Abstract::factory('term')->findAllBy(array('deleted' => 'no', 'vocabulary' => $this->id));

        foreach ($this->_oTags as $oTerm) {
            /* @var $oTerm AM_Model_Db_Term */
            $oTerm->setVocabulary($this);
        }

        return $this;
    }

    /**
     * Creates tag for current vocabulary
     * @param string $sTagName
     * @return AM_Model_Db_Term
     */
    public function createTag($sTagName)
    {
        $sTagName = trim(AM_Tools::filter_xss($sTagName));

        $oTagTerm = AM_Model_Db_Table_Abstract::factory('term')
                ->findOneBy(array('deleted' => 'no', 'vocabulary' => $this->id, 'title' => $sTagName));

        if (is_null($oTagTerm)) {
            $oTagTerm             = new AM_Model_Db_Term();
            $oTagTerm->title      = $sTagName;
            $oTagTerm->vocabulary = $this->id;
            $oTagTerm->save();
        }

        return $oTagTerm;
    }

    /**
     * Creates toc term for current vocabulary
     * @param string $sTocItemName
     * @param AM_Model_Db_Revision | null $oRevision
     * @param int | null $iParentId
     * @return AM_Model_Db_Term
     */
    public function createTocTerm($sTocItemName, AM_Model_Db_Revision $oRevision = null, $iParentId = null)
    {
        $sTocItemName = trim(AM_Tools::filter_xss($sTocItemName));
        $iParentId    = (0 == $iParentId)? null : $iParentId;

        $oTocTerm              = new AM_Model_Db_Term();
        $oTocTerm->title       = $sTocItemName;
        $oTocTerm->vocabulary  = $this->id;
        $oTocTerm->revision    = is_null($oRevision)? null : $oRevision->id;
        $oTocTerm->parent_term = $iParentId;
        $oTocTerm->updated     = new Zend_Db_Expr('NOW()');
        $oTocTerm->save();

        return $oTocTerm;
    }

    /**
     * Copy vocabulary and all his terms from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Term
     */
    public function copyToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        if (!empty($this->_oTags)) {
            $this->_oTags->copyToRevision($oRevisionTo);
        }

        if (!empty($this->_oToc)) {
            $this->_oToc->copyToRevision($oRevisionTo);
        }

        return $this;
    }

    /**
     * Move vocabulary and all his terms from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Term
     */
    public function moveToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        if (!empty($this->_oTags)) {
            $this->_oTags->moveToRevision($oRevisionTo);
        }

        if (!empty($this->_oToc)) {
            $this->_oToc->moveToRevision($oRevisionTo);
        }

        return $this;
    }
}
<?php
/**
 * @file
 * AM_Model_Db_Term_Data_Abstract class definition.
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
 * This class encapsulates logic of work with term's resources - files, strings, etc.
 * @todo Rename
 * @ingroup AM_Model
 */
abstract class AM_Model_Db_Term_Data_Abstract implements AM_Model_Db_Term_Data_Interface
{
    const TYPE = 'toc';

    /** @var AM_Model_Db_Term **/
    protected $_oTerm = null; /**< @type string */
    /** @var string **/
    protected $_sTempPath = null; /**< @type string */
    /** @var string **/
    protected $_sThumbSummary = null; /**< @type string */
    /** @var string **/
    protected $_sThumbStripe  = null; /**< @type string */

    public final function __construct(AM_Model_Db_Term $oTerm)
    {
        $this->_oTerm         = $oTerm;
        $this->_sThumbSummary = $oTerm->thumb_summary;
        $this->_sThumbStripe  = $oTerm->thumb_stripe;

        $this->init();
    }

    /**
     * Prepare data
     */
    protected function init()
    {}

    /**
     * @return AM_Model_Db_Term
     */
    protected function getTerm()
    {
        return $this->_oTerm;
    }

    /**
     * @see AM_Model_Db_Term_Data_Interface::copy()
     */
    public function copy()
    {}

    /**
     * @see AM_Model_Db_Element_Data_Interface::save()
     */
    public final function save()
    {
        $this->_preSave();

        $this->getTerm()->thumb_summary = pathinfo($this->_sThumbSummary, PATHINFO_BASENAME);
        $this->getTerm()->thumb_stripe  = pathinfo($this->_sThumbStripe, PATHINFO_BASENAME);
        $this->getTerm()->save();

        $this->_postSave();

        return $this;
    }

    /**
     * Pre save operations
     */
    protected function _preSave()
    { }

    /**
     * Post save operations
     */
    protected function _postSave()
    { }

    /**
     * Set temp path for uploaded files
     * @param string $sTempPath
     * @return AM_Model_Db_Element_Data_Abstract
     */
    public function setTempPath($sTempPath)
    {
        if (!AM_Tools_Standard::getInstance()->is_dir($sTempPath)){
            throw new AM_Model_Db_Element_Data_Exception("Wrong temp path given");
        }

        $this->_sTempPath = $sTempPath;

        return $this;
    }

    /**
     * Get temp path
     * @return string
     */
    public function getTempPath()
    {
        return $this->_sTempPath;
    }
}
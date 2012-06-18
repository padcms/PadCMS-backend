<?php
/**
 * @file
 * AM_Model_Db_Term_Data_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
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
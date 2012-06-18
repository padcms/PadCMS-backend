<?php
/**
 * @file
 * AM_Model_Db_IssueSimplePdf class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Issue simple pdf model class
 * Each page of this pdf is a page of issue
 * @ingroup AM_Model
 */
class AM_Model_Db_IssueSimplePdf extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_IssueSimplePdf_Data_Resource This object incapsulates logic of work with files */
    protected $_oResource = null; /**< @type AM_Model_Db_IssueSimplePdf_Data_Resource This object incapsulates logic of work with files */
    /** @var AM_Model_Db_Issue **/
    protected $_oIssue     = null; /**< @type AM_Model_Db_Issue */

    /**
     * Set issue instance
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Model_Db_IssueSimplePdf
     * @throws AM_Model_Db_Exception
     */
    public function setIssue(AM_Model_Db_Issue $oIssue)
    {
        $this->id_issue = $oIssue->id;
        $this->_oIssue  = $oIssue;

        return $this;
    }

    /**
     * Get issue instance
     * @return AM_Model_Db_Issue
     */
    public function getIssue()
    {
        if (is_null($this->_oIssue)) {
            $this->fetchIssue();
        }

        return $this->_oIssue;
    }

    /**
     * Fetch issue instance from db
     * @return AM_Model_Db_IssueSimplePdf
     * @throws AM_Model_Db_Exception
     */
    public function fetchIssue()
    {
        $this->_oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $this->id_issue);

        if (is_null($this->_oIssue)) {
            throw new AM_Model_Db_Exception(sprintf('Simple pdf "%s" has no issue', $this->id));
        }

        return $this;
    }

    /**
     * Set resource
     * @param AM_Model_Db_IssueSimplePdf_Data_Resource $oResource
     * @return AM_Model_Db_IssueSimplePdf
     */
    public function setResource(AM_Model_Db_IssueSimplePdf_Data_Resource $oResource)
    {
        $this->_oResource = $oResource;

        return $this;
    }

    /**
     * Get resource
     * @return AM_Model_Db_IssueSimplePdf_Data_Resource
     */
    public function getResource()
    {
        if (is_null($this->_oResource)) {
            $this->fetchResource();
        }

        return $this->_oResource;
    }

    /**
     * Fetch resource
     * @return AM_Model_Db_IssueSimplePdf
     */
    public function fetchResource()
    {
        $this->_oResource = new AM_Model_Db_IssueSimplePdf_Data_Resource($this);

        return $this;
    }

    /**
     * Upload resource
     * @return AM_Model_Db_IssueSimplePdf
     */
    public function uploadResource()
    {
        $this->getResource()->upload();

        $this->name = $this->getResource()->getResourceDbBaseName();
        $this->save();

        return $this;
    }

    /**
     * Get first page of PDF and convert it to the png
     * @return string
     */
    public function getFirstPageAsPng()
    {
        $sFilePath = $this->getResource()->getFirstPageAsPng();

        return $sFilePath;
    }

    /**
     * Get all page of PDF and convert them to the png
     * @return array
     */
    public function getAllPagesAsPng()
    {
        $aFilesPath = $this->getResource()->getAllPagesAsPng();

        return $aFilesPath;
    }

    /**
     * Allows post-delete logic to be applied to row.
     *
     * @return void
     */
    public function _postDelete()
    {
        $this->getResource()->delete();
    }
}
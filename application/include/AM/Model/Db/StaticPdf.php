<?php
/**
 * @file
 * AM_Model_Db_StaticPdf class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Horizontal pdf model class
 * @todo rename to HorizontalPdf
 * @ingroup AM_Model
 */
class AM_Model_Db_StaticPdf extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_StaticPdf_Data_Resource */
    protected $_oResources = null; /**< @type AM_Model_Db_StaticPdf_Data_Resource */
    /** @var AM_Model_Db_Issue **/
    protected $_oIssue     = null; /**< @type AM_Model_Db_Issue */

    const RESOURCE_TYPE = 'static-pdf';

    /**
     * Copy static pdf to new issue
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Model_Db_StaticPdf
     */
    public function copyToIssue(AM_Model_Db_Issue $oIssue)
    {
        $oResources = $this->getResource();

        $aData            = array();
        $aData['issue']   = $oIssue->id;
        $aData['updated'] = null;

        $this->copy($aData);
        $oResources->copy();

        return $this;
    }

    /**
     * Set issue instance
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Model_Db_StaticPdf
     * @throws AM_Model_Db_Exception
     */
    public function setIssue(AM_Model_Db_Issue $oIssue)
    {
        $this->issue   = $oIssue->id;
        $this->_oIssue = $oIssue;

        return $this;
    }

    /**
     * Get issue instance
     * @return AM_Model_Db_Issue
     */
    public function getIssue()
    {
        if (is_null($this->_oIssue)) {
            $this->_oIssue = $this->fetchIssue();
        }

        return $this->_oIssue;
    }

    /**
     * Fetch issue instance from db
     * @return AM_Model_Db_Issue
     * @throws AM_Model_Db_Exception
     */
    public function fetchIssue()
    {
        $this->_oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $this->issue);

        if (is_null($this->_oIssue)) {
            throw new AM_Model_Db_Exception(sprintf('Static pdf "%s" has no issue', $this->id));
        }

        return $this->_oIssue;
    }

    /**
     * Set resources
     * @param AM_Model_Db_StaticPdf_Data_Resource $oResources
     * @return AM_Model_Db_StaticPdf
     */
    public function setResources(AM_Model_Db_StaticPdf_Data_Resource $oResources)
    {
        $this->_oResources = $oResources;

        return $this;
    }

    /**
     * Get resources
     * @return AM_Model_Db_StaticPdf_Data_Resource
     */
    public function getResource()
    {
        if (is_null($this->_oResources)) {
            $this->fetchResource();
        }

        return $this->_oResources;
    }

    /**
     * Fetch resources
     * @return AM_Model_Db_StaticPdf
     */
    public function fetchResource()
    {
        $this->_oResources = new AM_Model_Db_StaticPdf_Data_Resource($this);

        return $this;
    }

    /**
     * Upload resource
     * @return AM_Model_Db_StaticPdf
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
    protected function _postDelete()
    {
        $this->getResource()->delete();

        $this->getIssue()->compileHorizontalPdfs();
    }
}
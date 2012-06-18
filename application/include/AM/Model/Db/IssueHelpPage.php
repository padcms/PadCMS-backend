<?php
/**
 * @file
 * AM_Model_Db_IssueHelpPage class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Help page for issue
 * @ingroup AM_Model
 */
class AM_Model_Db_IssueHelpPage extends AM_Model_Db_Abstract
{
    const TYPE_VERTICAL   = 'vertical';
    const TYPE_HORIZONTAL = 'horizontal';

    const HORIZONTAL_PREVIEW_RESOLUTION = '1024-768';
    const VERTICAL_PREVIEW_RESOLUTION   = '768-1024';

    /** @var AM_Model_Db_IssueHelpPage_Data_Resource This object incapsulates logic of work with files */
    protected $_oResource = null; /**< @type AM_Model_Db_IssueHelpPage_Data_Resource This object incapsulates logic of work with files */
    /** @var AM_Model_Db_Issue **/
    protected $_oIssue     = null; /**< @type AM_Model_Db_Issue */

    /**
     * Set issue instance
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Model_Db_IssueHelpPage
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
     * @return AM_Model_Db_IssueHelpPage
     * @throws AM_Model_Db_Exception
     */
    public function fetchIssue()
    {
        $this->_oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $this->id_issue);

        if (is_null($this->_oIssue)) {
            throw new AM_Model_Db_Exception(sprintf('Help page "%s" has no issue', $this->id));
        }

        return $this;
    }

    /**
     * Set resource
     * @param AM_Model_Db_IssueHelpPage_Data_Resource $oResource
     * @return AM_Model_Db_IssueHelpPage
     */
    public function setResource(AM_Model_Db_IssueHelpPage_Data_Resource $oResource)
    {
        $this->_oResource = $oResource;

        return $this;
    }

    /**
     * Get resource
     * @return AM_Model_Db_IssueHelpPage_Data_Resource
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
     * @return AM_Model_Db_IssueHelpPage
     */
    public function fetchResource()
    {
        $this->_oResource = new AM_Model_Db_IssueHelpPage_Data_Resource($this);

        return $this;
    }

    /**
     * Upload resource
     * @return AM_Model_Db_IssueHelpPage
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
     * Type setter
     * @param string $sType
     * @return AM_Model_Db_IssueHelpPage
     * @throws AM_Model_Db_Exception
     */
    public function setType($sType)
    {
        if (!in_array($sType, array(self::TYPE_HORIZONTAL, self::TYPE_VERTICAL))) {
            throw new AM_Model_Db_Exception(sprintf('Wrong help page type given "%s"', $sType));
        }

        $this->type = $sType;

        return $this;
    }

    /**
     * Retruns thumbnail preset name for resource
     * @return string
     */
    public function getThumbnailPresetType()
    {
        return AM_Model_Db_IssueHelpPage_Data_Abstract::TYPE . '-' . $this->type;
    }

    /**
     * Returns resolution for help page thumbnail
     * @return string
     */
    public function getResolutionForPreview()
    {
        switch ($this->type) {
            case self::TYPE_HORIZONTAL:
                return self::HORIZONTAL_PREVIEW_RESOLUTION;
            case self::TYPE_VERTICAL:
            default:
                return self::VERTICAL_PREVIEW_RESOLUTION;
        }
    }

    /**
     * Allows post-delete logic to be applied to row.
     *
     * @return void
     */
    protected function _postDelete()
    {
        $this->getResource()->delete();
    }
}
<?php
/**
 * @file
 * AM_Model_Db_IssueHelpPage_Data_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class encapsulates logic of work with help page's resources
 * @todo Rename
 * @ingroup AM_Model
 */
abstract class AM_Model_Db_IssueHelpPage_Data_Abstract implements AM_Model_Db_IssueHelpPage_Data_Interface
{
    const TYPE = 'issue-help-page';

    /** @var AM_Model_Db_IssueHelpPage **/
    protected $_oIssueHelpPage = null; /**< @type AM_Model_Db_IssueHelpPage */

    /**
     * @param AM_Model_Db_IssueHelpPage $oIssueHelpPage
     * @throws AM_Model_Db_IssueHelpPage_Data_Exception
     */
    public final function __construct(AM_Model_Db_IssueHelpPage $oIssueHelpPage)
    {
        $this->_oIssueHelpPage = $oIssueHelpPage;

        $this->_init();
    }

    /**
     * Prepare data
     */
    protected function _init()
    {}

    /**
     * @return AM_Model_Db_IssueHelpPage
     */
    protected function _getIssueHelpPage()
    {
        return $this->_oIssueHelpPage;
    }

    /**
     * @see AM_Model_Db_IssueHelpPage_Data_Interface::upload()
     */
    public function upload()
    {}

    /**
     * @see AM_Model_Db_IssueHelpPage_Data_Interface::delete()
     */
    public function delete()
    {
        $this->_postDelete();
    }

    /**
     * Allows post-delete logic to be applied to resource.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _postDelete()
    {

    }
}
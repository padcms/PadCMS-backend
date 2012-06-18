<?php
/**
 * @file
 * AM_Model_Db_IssueSimplePdf_Data_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class encapsulates logic of work with simple pdf's resources - files, strings, etc.
 * @todo Rename
 * @ingroup AM_Model
 */
abstract class AM_Model_Db_IssueSimplePdf_Data_Abstract implements AM_Model_Db_IssueSimplePdf_Data_Interface
{
    const TYPE = 'issue-simple-pdf';

    /** @var AM_Model_Db_IssueSimplePdf **/
    protected $_oIssueSimplePdf = null; /**< @type AM_Model_Db_IssueSimplePdf */

    /**
     * @param AM_Model_Db_IssueSimplePdf $oIssueSimplePdf
     * @throws AM_Model_Db_IssueSimplePdf_Data_Exception
     */
    public final function __construct(AM_Model_Db_IssueSimplePdf $oIssueSimplePdf)
    {
        $this->_oIssueSimplePdf = $oIssueSimplePdf;

        $this->_init();
    }

    /**
     * Prepare data
     */
    protected function _init()
    {}

    /**
     * @return AM_Model_Db_IssueSimplePdf
     */
    protected function _getIssueSimplePdf()
    {
        return $this->_oIssueSimplePdf;
    }

    /**
     * @see AM_Model_Db_IssueSimplePdf_Data_Interface::upload()
     */
    public function upload()
    {}

    /**
     * @see AM_Model_Db_IssueSimplePdf_Data_Interface::delete()
     */
    public final function delete()
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
    { }
}
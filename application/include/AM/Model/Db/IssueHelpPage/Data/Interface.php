<?php
/**
 * @file
 * AM_Model_Db_IssueHelpPage_Data_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class encapsulates logic of work with help page's resources - files, strings, etc.
 * @todo Rename
 * @ingroup AM_Model
 */
interface AM_Model_Db_IssueHelpPage_Data_Interface
{
    /**
     * @param AM_Model_Db_IssueSimplePdf $oIssueHelpPage
     */
    public function __construct(AM_Model_Db_IssueHelpPage $oIssueHelpPage);

    /**
     * Upload resources
     */
    public function upload();

    /**
     * Delete resource
     */
    public function delete();
}
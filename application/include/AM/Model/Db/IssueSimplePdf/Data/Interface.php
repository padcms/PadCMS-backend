<?php
/**
 * @file
 * AM_Model_Db_IssueSimplePdf_Data_Interface class definition.
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
interface AM_Model_Db_IssueSimplePdf_Data_Interface
{
    /**
     * @param AM_Model_Db_IssueSimplePdf $oIssueSimplePdf
     */
    public function __construct(AM_Model_Db_IssueSimplePdf $oIssueSimplePdf);

    /**
     * Upload resources
     */
    public function upload();

    /**
     * Delete resource
     */
    public function delete();
}
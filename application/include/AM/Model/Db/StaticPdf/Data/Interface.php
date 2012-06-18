<?php
/**
 * @file
 * AM_Model_Db_StaticPdf_Data_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class encapsulates logic of work with horisontal pdf's resources - files, strings, etc.
 * @todo Rename
 * @ingroup AM_Model
 */
interface AM_Model_Db_StaticPdf_Data_Interface
{
    /**
     * @param AM_Model_Db_StaticPdf $oStaticPdf
     */
    public function __construct(AM_Model_Db_StaticPdf $oStaticPdf);

    /**
     * Copy data
     */
    public function copy();

    /**
     * Upload resources
     */
    public function upload();

    /**
     * Delete resource
     */
    public function delete();
}
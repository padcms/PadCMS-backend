<?php
/**
 * @file
 * AM_Model_Db_Term_Data_Interface class definition.
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
interface AM_Model_Db_Term_Data_Interface
{
    /**
     * @param AM_Model_Db_Term $oTerm
     */
    public function __construct(AM_Model_Db_Term $oTerm);

    /**
     * Copy data
     */
    public function copy();

    /**
     * Save data
     */
    public function save();

}
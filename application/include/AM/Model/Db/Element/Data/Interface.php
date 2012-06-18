<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class encapsulates logic of work with element's resources - files, strings, etc.
 * @ingroup AM_Model
 */
interface AM_Model_Db_Element_Data_Interface
{
    /**
     * @param AM_Model_Db_Element $oElement
     */
    public function __construct(AM_Model_Db_Element $oElement);

    /**
     * Remove all elements data and cretes new from array
     * @var array $aData
     */
    public function setData($aData);

    /**
     * Add key-value data to the element
     * @param string $sKey
     * @param mixed $mValue
     */
    public function addKeyValue($sKey, $mValue);

    /**
     * Get data
     * @return array
     */
    public function getData();

    /**
     * Copy data
     */
    public function copy();

    /**
     * Save extra data
     */
    public function save();

    /**
     * Delete extra data
     */
    public function delete();
}
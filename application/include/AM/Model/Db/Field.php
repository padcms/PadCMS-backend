<?php
/**
 * @file
 * AM_Model_Db_Field class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Field model class
 * Each page contains few layers (fields) with a certain type
 * @ingroup AM_Model
 */
class AM_Model_Db_Field extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_FieldType **/
    protected $_oFieldType = null;

    /**
     * Get field type
     * @return AM_Model_Db_FieldType
     */
    public function getFieldType()
    {
        if (is_null($this->_oFieldType)) {
            $this->fetchFieldType();
        }

        return $this->_oFieldType;
    }

    /**
     * Fetch field type
     * @return AM_Model_Db_Field
     */
    public function fetchFieldType()
    {
        $this->_oFieldType = AM_Model_Db_Table_Abstract::factory('field_type')->findOneBy('id', $this->field_type);

        if (is_null($this->_oFieldType)) {
            throw new AM_Model_Db_Exception(sprintf('Field "%d" has no type', $this->id));
        }

        return $this;
    }
}
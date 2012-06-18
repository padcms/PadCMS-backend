<?php
/**
 * @file
 * AM_Model_Db_Mock class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The model's stub
 * @ingroup AM_Model
 */
class AM_Model_Db_Mock extends AM_Model_Db_Abstract
{
    public function __construct($config = array())
    {
        $this->_data = array('id' => null, 'title' => null);
    }

    protected function _getTableColumns()
    {
        return array('id', 'title');
    }
}
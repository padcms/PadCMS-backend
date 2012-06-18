<?php
/**
 * @file
 * AM_Model_Db_Device class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Device model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Device extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_User **/
    protected $_oUser = null; /**< @type AM_Model_Db_User */

    /**
     * Get user
     * @return AM_Model_Db_User|null
     */
    public function getUser()
    {
        if (is_null($this->_oUser)) {
            $this->_oUser = AM_Model_Db_Table_Abstract::factory('user')->findOneBy(array('id' => $this->user));
        }

        return $this->_oUser;
    }

}
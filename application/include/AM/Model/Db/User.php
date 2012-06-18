<?php
/**
 * @file
 * AM_Model_Db_User class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Term page model class
 * @ingroup AM_Model
 */
class AM_Model_Db_User extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_Client **/
    protected $_oClient = null; /**< @type AM_Model_Db_Client **/



    /**
     * Gets user's client
     *
     * @return AM_Model_Db_Client
     */
    public function getClient()
    {
        if (empty($this->_oClient)) {
            $this->fetchClient();
        }
        return $this->_oClient;
    }

    /**
     * Fetchs user's client
     *
     * @throws AM_Model_Db_Exception
     * @return AM_Model_Db_User
     */
    public function fetchClient()
    {
        $this->_oClient = AM_Model_Db_Table_Abstract::factory('client')->findOneBy(array('id' => $this->client));

        if (empty($this->_oClient)) {
            throw new AM_Model_Db_Exception(sprintf('User "%s" has no client', $this->id));
        }

        return $this;
    }

    /**
     * Delete user
     */
    public function delete()
    {
        //@todo remove all user's resources
        $this->deleted = 'yes';
        $this->save();
    }
}
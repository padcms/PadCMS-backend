<?php
/**
 * @file
 * AM_Model_Db_Client class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Client model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Client extends AM_Model_Db_Abstract
{
    /**
     * Soft deleted client
     */
    public function delete()
    {
        $oUsers = AM_Model_Db_Table_Abstract::factory('user')->findAllBy(array('client' => $this->id));
        $oUsers->delete();

        $oApplications = AM_Model_Db_Table_Abstract::factory('application')->findAllBy(array('client' => $this->id));
        $oApplications->delete();

        $this->deleted = 'yes';
        $this->save();
    }
}
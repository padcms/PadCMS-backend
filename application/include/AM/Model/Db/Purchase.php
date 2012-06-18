<?php
/**
 * @file
 * AM_Model_Db_Purchase class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Purchase model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Purchase extends AM_Model_Db_Abstract
{
    /**
     * Checks if subscription has been expired
     * @return boolean
     */
    public function isExpired()
    {
        if (!empty($this->expires_date)) {
            $oExpiredDate = new Zend_Date($this->expires_date);
            $oNowDate     = new Zend_Date();

            if ($oExpiredDate->isEarlier($oNowDate)) {
                $this->delete();

                return true;
            }
        }

        return false;
    }
}
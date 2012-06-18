<?php
/**
 * @file
 * AM_Api_Apns class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class is responsible for saving device token for apple push notification service
 * @ingroup AM_Api
 */
class AM_Api_Apns extends AM_Api
{
    const RESULT_SUCCESS       = 1;
    const RESULT_RECORD_EXISTS = 2;

    /**
     * Saves device token, which will use to send notifications
     *
     * @param string $sUdid
     * @param int $iApplicationId
     * @param string $sToken
     * @return void
     * @throws AM_Api_Apns_Exception
     */
    public function setDeviceToken($sUdid, $iApplicationId, $sToken)
    {
        $sUdid          = trim($sUdid);
        $iApplicationId = intval($iApplicationId);
        $sToken         = trim($sToken);

        if (empty($sUdid)) {
            throw new AM_Api_Apns_Exception(sprintf('Invalid UDID given: "%s"', $sUdid));
        }

        if (empty($sToken)) {
            throw new AM_Api_Apns_Exception(sprintf('Invalid token given: "%s"', $sToken));
        }

        $oDeviceToken = AM_Model_Db_Table_Abstract::factory('device_token')
                ->findOneBy(array('udid'           => $sUdid,
                                  'token'          => $sToken,
                                  'application_id' => $iApplicationId));

        if (!is_null($oDeviceToken)) {
            $oDeviceToken->token = $sToken;
            $oDeviceToken->save();
            return array('code' => self::RESULT_RECORD_EXISTS);
        }

        $oDeviceToken                 = new AM_Model_Db_DeviceToken();
        $oDeviceToken->udid           = $sUdid;
        $oDeviceToken->token          = $sToken;
        $oDeviceToken->application_id = $iApplicationId;
        $oDeviceToken->save();

        return array('code' => self::RESULT_SUCCESS);
    }
}

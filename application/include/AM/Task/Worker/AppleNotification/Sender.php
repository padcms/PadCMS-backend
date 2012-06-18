<?php
/**
 * @file
 * AM_Task_Worker_AppleNotification_Sender class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Task for sending push notification
 * @ingroup AM_Task
 */
class AM_Task_Worker_AppleNotification_Sender extends AM_Task_Worker_Abstract
{
    /** @var ApnsPHP_Push **/
    protected $_oPushService = null; /**< @type ApnsPHP_Push */

    /**
     * @see AM_Task_Worker_Abstract::_fire()
     * @throws AM_Task_Worker_Exception
     * @return void
     */
    protected function _fire()
    {
        $aTokens  = (array) $this->getOption('tokens');
        $sMessage = $this->getOption('message');
        $iBadge   = intval($this->getOption('badge'));

        // Instanciate a new ApnsPHP_Push object
        $oPushService = $this->getPushService();

        // Connect to the Apple Push Notification Service
        $oPushService->connect();

        foreach ($aTokens as $sToken) {
            // Instantiate a new Message with a single recipient
            $oPushMessage = new ApnsPHP_Message($sToken);

            $oPushMessage->setText($sMessage);
            $oPushMessage->setBadge($iBadge);

            // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
            // over a ApnsPHP_Message object retrieved with the getErrors() message.
            $oPushMessage->setCustomIdentifier(sprintf('%s', $sToken));

            // Add the message to the message queue
            $oPushService->add($oPushMessage);
        }

        // Send all messages in the message queue
        $oPushService->send();

        // Disconnect from the Apple Push Notification Service
        $oPushService->disconnect();

        // Examine the error message container
        $aErrorQueue = $oPushService->getErrors();
        if (!empty($aErrorQueue)) {
            $aErrors = array();
            foreach ($aErrorQueue as $aError) {
                /* @var $oMessage ApnsPHP_Message */
                $oMessage      = $aError['MESSAGE'];
                //Get last error message
                $aMessageError = array_pop($aError['ERRORS']);
                $aErrors[$oMessage->getCustomIdentifier()] = $aMessageError;
            }
            $this->addOption('errors', $aErrors);
            throw new AM_Task_Worker_Exception('Messages have an unrecoverable errors');
        }
    }

    /**
     * Set APNS push service
     * @param ApnsPHP_Push $oPushService
     * @return AM_Task_Worker_AppleNotification_Sender
     */
    public function setPushService(ApnsPHP_Push $oPushService)
    {
        $this->_oPushService = $oPushService;

        return $this;
    }

    /**
     * Get APNS push service
     * @return ApnsPHP_Push
     */
    public function getPushService()
    {
        if (is_null($this->_oPushService)) {
            $iApplicationId = intval($this->getOption('application_id'));
            if (empty($iApplicationId)) {
                throw new AM_Task_Worker_Exception('Wrong application id given');
            }

            $sEnvironment         = $this->getConfig()->apns->environment;
            $sCertificateRootPath = rtrim($this->getConfig()->apns->cerificate_path, DIRECTORY_SEPARATOR);

            $sCertificateRootPath .= DIRECTORY_SEPARATOR . $iApplicationId . '_' . $sEnvironment . '.pem';

            $this->_oPushService = new ApnsPHP_Push(
                            constant('ApnsPHP_Abstract::' . Zend_Filter::filterStatic('ENVIRONMENT_'.$sEnvironment, 'StringToUpper')),
                            $sCertificateRootPath
            );
        }

        return $this->_oPushService;
    }
}
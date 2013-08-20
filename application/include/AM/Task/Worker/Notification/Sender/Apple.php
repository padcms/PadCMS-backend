<?php
/**
 * @file
 * AM_Task_Worker_Notification_Sender_Apple class definition.
 *
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * Task for sending push notification
 * @ingroup AM_Task
 */
class AM_Task_Worker_Notification_Sender_Apple extends AM_Task_Worker_Abstract
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
     * @return AM_Task_Worker_Notification_Sender_Apple
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
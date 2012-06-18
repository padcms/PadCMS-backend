<?php
/**
 * @file
 * AM_Task_Worker_AppleNotification_Feedback class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Task for checking tokens
 * @ingroup AM_Task
 */
class AM_Task_Worker_AppleNotification_Feedback extends AM_Task_Worker_Abstract
{
    /** @var ApnsPHP_Feedback **/
    protected $_oFeedbackService = null; /**< @type ApnsPHP_Feedback */

    /**
     * @see AM_Task_Worker_Abstract::_fire()
     * @throws AM_Task_Worker_Exception
     * @return void
     */
    protected function _fire()
    {
        $this->getFeedbackService()->connect();

        $aDeviceTokens = $this->getFeedbackService()->receive();

        if (!empty($aDeviceTokens)) {
            var_dump($aDeviceTokens);
        }

        // Disconnect from the Apple Push Notification Feedback Service
        $this->getFeedbackService()->disconnect();
    }

    /**
     * Set APNS feedback service
     * @param ApnsPHP_Feedback $oFeedbackService
     * @return AM_Task_Worker_AppleNotification_Feedback
     */
    public function setFeedbackService(ApnsPHP_Feedback $oFeedbackService)
    {
        $this->_oFeedbackService = $oFeedbackService;

        return $this;
    }

    /**
     * Get APNS feedback service
     * @return ApnsPHP_Feedback
     */
    public function getFeedbackService()
    {
        if (is_null($this->_oFeedbackService)) {
            $iApplicationId = intval($this->getOption('application_id'));
            if (empty($iApplicationId)) {
                throw new AM_Task_Worker_Exception('Wrong application id given');
            }

            $sEnvironment         = $this->getConfig()->apns->environment;
            $sCertificateRootPath = rtrim($this->getConfig()->apns->cerificate_path, DIRECTORY_SEPARATOR);

            $sCertificateRootPath .= DIRECTORY_SEPARATOR . $iApplicationId . '_' . $sEnvironment . '.pem';

            $this->_oFeedbackService = new ApnsPHP_Feedback(
                            constant('ApnsPHP_Abstract::' . Zend_Filter::filterStatic('ENVIRONMENT_'.$sEnvironment, 'StringToUpper')),
                            $sCertificateRootPath
            );
        }

        return $this->_oFeedbackService;
    }
}
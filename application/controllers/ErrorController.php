<?php
/**
 * @file
 * ErrorController class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Controller_Action
 */
class ErrorController extends AM_Controller_Action
{
    /**
     * Error action
     */
    public function errorAction()
    {
        $iHttpResponceCode = null;
        $sMessage          = null;

        $oError     = $this->_getParam('error_handler');
        /* @var $oError ArrayObject */
        $oException = $oError->exception;
        /* @var $oException Exception */
        switch (get_class($oException)) {
            case 'Zend_Controller_Action_Exception':
            case 'AM_Controller_Exception':
            case 'AM_Controller_Exception_NotFound':
                $iHttpResponceCode = 404;
                $sMessage           = 'Not found';
                break;

            case 'AM_Controller_Exception_Forbidden':
                $iHttpResponceCode = 404;
                $sMessage           = 'Access denied';
                break;

            case 'AM_Controller_Exception_BadRequest':
                $iHttpResponceCode = 404;
                $sMessage           = 'Bad request';
                break;

            default:
                $iHttpResponceCode = 404;
                $sMessage           = 'Not found';
        }

        $sErrorCode = uniqid('_', true);
        $this->getLogger()->crit(sprintf('URI: %s MESSAGE: %s EXECEPTION: %s', $this->getRequest()->getRequestUri(), $oException->getMessage(), $oException->__toString()), array('file' => 'ErrorController', 'info' => $sErrorCode));
        $this->getResponse()->clearBody();
        $this->getResponse()->setHttpResponseCode($iHttpResponceCode);

        $this->view->message          = $sMessage;
        $this->view->httpResponceCode = $iHttpResponceCode;
        $this->view->errorCode        = $sErrorCode;
        $this->view->description      = null;

        if (APPLICATION_ENV == 'development') {
            $this->view->description = $oException->getTraceAsString();
        }
    }
}

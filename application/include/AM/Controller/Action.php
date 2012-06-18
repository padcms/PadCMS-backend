<?php
/**
 * @file
 * AM_Controller_Action class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Controller_Action
 */

/**
 * This is the superclass for all Controller Action classes
 *
 * @ingroup AM_Controller_Action
 * @todo refatoring
 */
class AM_Controller_Action extends Zend_Controller_Action
{
    /** @var Zend_Auth */
    public $oAcl = null; /**< @type Zend_Auth */

    /** @var array User information */
    protected $_aUserInfo; /**< @type array User information */

    /** @var Zend_Log*/
    protected $_oLogger = null; /**< @type Zend_Log */

    /** @var Zend_Config */
    public $oConfig = null; /**< @type Zend_Config */

    /** @var Zend_Db_Adapter_Abstract */
    public $oDb = null; /**< @type Zend_Db_Adapter_Abstract */

    /** @var Zend_Session_Namespace */
    public $oSession = null; /**< @type Zend_Session_Namespace */

    function preDispatch()
    {
        parent::preDispatch();

        $this->view->sessionId = Zend_Session::getId();

        $this->oConfig  = Zend_Registry::get('config');
        $this->oDb      = Zend_Registry::get('db');
        $this->oSession = new Zend_Session_Namespace(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('applicationName'));
        $this->oAcl     = Zend_Auth::getInstance();

        if (APPLICATION_ENV == 'development') {
            $this->_activateFirebug();
        }

        $this->view->auth       = $this->getUser();
        $this->view->controller = $this->getRequest()->getControllerName();
        $this->view->action     = $this->getRequest()->getActionName();

        $this->_aUserInfo     = $this->getUser();
        $this->view->userInfo = $this->_aUserInfo;
    }

    /**
     * Firebug activation
     */
    private function _activateFirebug()
    {
        $oProfiler = new Zend_Db_Profiler_Firebug('DB Queries');
        $oProfiler->setEnabled(true);
        $this->oDb->setProfiler($oProfiler);
    }

    /**
     * Get logged user information
     *
     * @return array
     */
    public function getUser()
    {
        return Zend_Auth::getInstance()->getIdentity();
    }

    /**
     * Get the logger instance
     *
     * @return Zend_Log
     */
    public function getLogger()
    {
        $this->_oLogger = Zend_Registry::get('log');
        $this->_oLogger->setEventItem('file', get_class($this));

        return $this->_oLogger;
    }

    /**
     * Make redirect and exit
     *
     * @param string $url Redirect URL
     * @param boolean $prependBase Prepend base
     */
    public function doRedirect($url, $prependBase = true) {
        $this->_redirect($url, array('exit' => true, 'prependBase' => $prependBase));
    }

    /**
     * Translate text
     *
     * @param string $text
     * @return string
     */
    public function __($text)
    {
        return $this->localizer->translate($text);
    }
}

<?php
/**
 * @file
 * AM_Controller_Action class definition.
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

    /**
     * Sends the JSON response as text/plain
     * @param array $aData
     */
    public function sendJsonAsPlainText($aData)
    {
        $aData = $this->getHelper('Json')->direct($aData, false);

        $oResponse = $this->getResponse();
        $oResponse->setHeader('Content-Type', 'text/plain', true);
        $oResponse->setBody($aData);

        $oResponse->sendResponse();
        exit;
    }
}

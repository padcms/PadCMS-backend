<?php
/**
 * @file
 * AM_Controller_Plugin_Acl class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Acl
 */
class AM_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /** @var Zend_Acl */
    private $_oAcl = null; /**< @type Zend_Acl */

    /**
     * @param Zend_Acl $oAcl
     */
    public function __construct(Zend_Acl $oAcl)
    {
        $this->_oAcl = $oAcl;
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('airmag'));
    }

    /**
     * @param Zend_Controller_Request_Abstract $oHttpRequest
     */
    public function preDispatch(Zend_Controller_Request_Abstract $oHttpRequest)
    {
        $sControllerName   = $oHttpRequest->getControllerName();
        $sActionName       = $oHttpRequest->getActionName();
        $aRequestedParams  = $oHttpRequest->getUserParams();
        $sQuery            = '';

        unset($aRequestedParams['controller']);
        unset($aRequestedParams['action']);

        // Define user role
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $aData = Zend_Auth::getInstance()->getStorage()->read();
            $sRole = $aData['role'];
        } else {
            // Default role
            $sRole = 'guest';
        }

        // Check access
        if (!$this->_oAcl->isAllowed($sRole, $sControllerName, $sActionName)) {
            $oHttpRequest->setParam('referer_controller', $sControllerName);
            $oHttpRequest->setParam('referer_action', $sActionName);

            $aParams = array();
            if (count($aRequestedParams)) {
                foreach ($aRequestedParams as $sKey => $sValue) {
                    $aParams[] = $sKey;
                    $aParams[] = $sValue;
                }

                $sQuery = implode('/', $aParams) . '/';
            }


            $oHttpRequest->setParam('query', $sQuery);
            $oHttpRequest->setControllerName('auth')->setActionName('login');
            $this->_response->setHttpResponseCode(401);
        }
    }
}
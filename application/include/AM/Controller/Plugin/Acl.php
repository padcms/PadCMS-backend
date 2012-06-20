<?php
/**
 * @file
 * AM_Controller_Plugin_Acl class definition.
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
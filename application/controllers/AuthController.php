<?php
/**
 * @file
 * AuthController class definition.
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
class AuthController extends AM_Controller_Action
{
    /**
     * Auth index action
     */
    public function indexAction()
    {
        $this->_forward('login');
    }

    /**
     * Auth login action
     */
    public function loginAction()
    {
        $oLoginComponent = new AM_Component_Record_Auth_Login($this);

        if ($oLoginComponent->operation()) {
            $sRefererController = $this->_getParam('referer_controller', '');

            if ($sRefererController) {
                $sRefererAction = $this->_getParam('referer_action', '');
                $sQuery         = $this->_getParam('query', '');

                return $this->_redirect('/' . $sRefererController . '/' . $sRefererAction . '/' . $sQuery);
            }

            return $this->_redirect('/index');

        } else {
            $oLoginComponent->show();
        }
    }

    /**
     * Auth logout action
     */
    public function logoutAction()
    {
        if ($this->oAcl->hasIdentity()) {
            $this->oAcl->clearIdentity();
        }

        return $this->_redirect('/');
    }
}
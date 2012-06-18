<?php
/**
 * @file
 * AM_Component_Record_Auth_Login class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Login record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Auth_Login extends Volcano_Component_Record
{
    /**
     * @param AM_Controller_Action $oActionController
     */
    public function  __construct(AM_Controller_Action $oActionController)
    {
        $aControls   = array();
        $aControls[] = new Volcano_Component_Control($oActionController, 'login', 'Login',array(array('require')));
        $aControls[] = new Volcano_Component_Control_Password($oActionController, 'password', 'Password',array(array('require')));

        parent::__construct($oActionController, 'component', $aControls);
    }

    /**
     * @return boolean
     */
    public function isSubmitted()
    {
        return $this->isSubmitted;
    }

    /**
     * @return boolean
     */
    public function operation()
    {
        return $this->isSubmitted ? $this->validate() : false;
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        $this->actionController->oAcl->getStorage()->clear();

        if (!parent::validate()) {
            return false;
        }

        $sUserLogin    = $this->controls['login']->getValue();
        $sUserPassword = $this->controls['password']->getValue();

        $oAuth = Zend_Auth::getInstance();

        $oAuthAdapter = new Zend_Auth_Adapter_DbTable();
        $oAuthAdapter->setTableName('user')
                ->setIdentityColumn('login')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('MD5(?)');

        $oAuthAdapter->setIdentity($sUserLogin)
                ->setCredential($sUserPassword);

        $oSelect = $oAuthAdapter->getDbSelect();
        $oSelect->where('user.deleted = ?', 'no')
                ->joinLeft('client', 'client.id = user.client', array('client_title' => 'client.title'));

        $oResult = $oAuth->authenticate($oAuthAdapter);

        if ($oResult->isValid()) {
            $aResult         = (array) $oAuthAdapter->getResultRowObject();
            $aResult['role'] = ($aResult['is_admin'] == 0) ? 'user' : 'admin';
            $oAuth->getStorage()->write($aResult);

            return true;
        } else {
            $this->errors[] = 'Invalid login or password';

            return false;
        }
    }

    /**
     * @return void
     */
    public function show()
    {
        $this->view->errors = $this->getErrors();

        return parent::show();
    }
}
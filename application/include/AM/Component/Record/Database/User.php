<?php
/**
 * @file
 * AM_Component_Record_Database_User class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * User record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database_User extends AM_Component_Record_Database
{
    /**
     *
     * @param AM_Controller_Action $oActionController
     * @param string $sName
     * @param int $iUserId
     * @param int $iClientId
     * @return void
     */
    public function  __construct(AM_Controller_Action $oActionController, $sName, $iUserId, $iClientId)
    {
        $this->config = $oActionController->oConfig;

        if (!$iClientId) {
            $oQuery    = $oActionController->oDb->select()->from('user', 'client')->where('id = ?', $iUserId);
            $iClientId = $oActionController->oDb->fetchOne($oQuery);
        }

        $aControls = array();

        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'client', $iClientId);
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'login', 'Login', array(array('require')), 'login');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'first_name', 'First name', array(array('require')), 'first_name');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'last_name', 'Last name', array(array('require')), 'last_name');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'email', 'Email', array(array('require'), array('email')), 'email');
        $aControls[] = new Volcano_Component_Control_Database_Password($oActionController, 'password', 'Password', array(array('require')));
        $aControls[] = new Volcano_Component_Control_Password($oActionController, 'repeat_password', 'Confirm', array(array('require')));

        return parent::__construct($oActionController, $sName, $aControls, $oActionController->oDb, 'user', 'id', $iUserId);
    }

    /**
     * @return boolean
     */
    public function  validate()
    {
        if (!parent::validate()) {
            return false;
        }

        // Check is login uniq
        if (!$this->primaryKeyValue) {
            $oQuery = $this->db->select()->from('user', 'id')
                    ->where('login = ?', trim($this->controls['login']->getValue()))
                    ->where('deleted != ?','yes');
            if ($this->db->fetchOne($oQuery)) {
                $this->errors[] = $this->localizer->translate('User with such login already exists');

                return false;
            }
        }

        // Check password confirm
        if ($this->controls['password']->getValue() != $this->controls['repeat_password']->getValue()) {
            $this->errors[] = $this->localizer->translate('Passwords does not match');

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function operation()
    {
        if($sResult = parent::operation()) {
            $aVariables                = array();
            $aVariables['subject']     = "[PadCMS] Your account has been " . (($sResult == 'update') ? 'updated' : 'created') . ".";
            $aVariables['emails']      = $this->controls['email']->getValue();
            $aVariables['firstname']   = $this->controls['first_name']->getValue();
            $aVariables['service_url'] = $this->config->common->base_domain_protocol . $this->config->common->base_domain;
            $aVariables['login']       = $this->controls['login']->getValue();
            $aVariables['password']    = $this->controls['password']->getValue();

            $this->actionController->getHelper('mailer')->send('notifications', $aVariables);
        }

        return $sResult;
    }
}
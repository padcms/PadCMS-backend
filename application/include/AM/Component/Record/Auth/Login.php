<?php
/**
 * @file
 * AM_Component_Record_Auth_Login class definition.
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
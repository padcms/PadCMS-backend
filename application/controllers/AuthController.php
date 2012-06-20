<?php
/**
 * @file
 * AuthController class definition.
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
<?php
/**
 * @file
 * IndexController class definition.
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
class IndexController extends AM_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $oUrlHelper = $this->getHelper('Url');

        if ($this->_aUserInfo['role'] == 'admin') {
            return $this->_redirect( $oUrlHelper->url(array('controller' => 'client', 'action' => 'list')) );
        }

        return $this->_redirect( $oUrlHelper->url(array('controller' => 'application', 'action' => 'list')) );
    }
}
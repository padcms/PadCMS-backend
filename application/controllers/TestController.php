<?php
/**
 * @file
 * TestController class definition.
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
class TestController extends AM_Controller_Action
{
    /*
     * Test index action
     */
    public function indexAction()
    {
        $this->_redirect('/test/api');
    }

    /*
     * Test api action
     */
    public function apiAction()
    {

    }
}
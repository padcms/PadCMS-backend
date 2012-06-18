<?php
/**
 * @file
 * AM_Application_Resource_Acl class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the ACL
 * @ingroup AM_Application
 */
class AM_Application_Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return void
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('FrontController');

        $aOptions = $this->getOptions();

        $sAclClass = array_key_exists('class', $aOptions) ? $aOptions['class'] : 'AM_Acl';

        $this->getBootstrap()->getResource('FrontController')->registerPlugin(new AM_Controller_Plugin_Acl(new $sAclClass()));
    }
}

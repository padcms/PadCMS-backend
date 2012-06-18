<?php
/**
 * @file
 * AM_Application_Resource_Errorhandler class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the Zend_Controller_Plugin_ErrorHandler
 * @ingroup AM_Application
 */
class AM_Application_Resource_Errorhandler extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return void
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('FrontController');

        $this->getBootstrap()->getResource('FrontController')->registerPlugin(new Zend_Controller_Plugin_ErrorHandler($this->getOptions()), 100);
    }
}

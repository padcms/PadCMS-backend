<?php
/**
 * @file
 * AM_Application_Resource_Smarty class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for Smarty
 * @ingroup AM_Application
 */
class AM_Application_Resource_Smarty extends Zend_Application_Resource_ResourceAbstract
{
    /** @var AM_Controller_Action_Helper_Smarty */
    protected $_oActionHelper = null; /**< @type AM_Controller_Action_Helper_Smarty */
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return AM_Controller_Action_Helper_Smarty
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('frontcontroller');

        if (is_null($this->_oActionHelper)) {
            $this->_oActionHelper = new AM_Controller_Action_Helper_Smarty($this->getOptions());
            Zend_Controller_Action_HelperBroker::addHelper($this->_oActionHelper);
        }

        return $this->_oActionHelper;
    }
}

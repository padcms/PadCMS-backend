<?php
/**
 * @file
 * AM_Application_Resource_Localizer class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the Zend_Date
 * @todo refactoring - use Zend_Translate
 * @ingroup AM_Application
 */
class AM_Application_Resource_Localizer extends Zend_Application_Resource_ResourceAbstract
{
    /** @var AM_Controller_Action_Helper_Localizer */
    protected $_oActionHelper = null; /**< @type AM_Controller_Action_Helper_Localizer */

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return AM_Controller_Action_Helper_Localizer
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('frontcontroller');

        if (is_null($this->_oActionHelper)) {
            $aOptions             = $this->getOptions();
            $this->_oActionHelper = new AM_Controller_Action_Helper_Localizer($aOptions['type'], $aOptions['languages'], 'language', false, 'language');
            Zend_Controller_Action_HelperBroker::addHelper($this->_oActionHelper);
        }

        return $this->_oActionHelper;
    }
}

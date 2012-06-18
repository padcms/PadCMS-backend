<?php
/**
 * @file
 * AM_Component class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Component
 */

/**
 * This is the superclass for all components
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component
{
    /** @var AM_Controller_Action_Helper_Smarty */
    protected $_oView; /**< @type Zend_Db_Adapter_Abstract */

    /** @var Zend_Db_Adapter_Abstract */
    protected $_oDbAdapter = null; /**< @type Zend_Db_Adapter_Abstract */

    /** @var AM_Controller_Action */
    protected $_oControllerAction = null; /**< @type AM_Controller_Action */

    /** @var Zend_Config */
    protected $_oConfig = null; /**< @type Zend_Config */

    /** @var bool */
    protected $_bIsSubmitted = false; /**< @type bool */

    /**  @var Volcano_Localizer */
    protected $_oLocalizer = null; /**< @type Volcano_Localizer */

    /** @var string Component name */
    protected $_sName; /**< @type string Component name */

    /** @var array */
    protected $_aErrors = array();/**< @type array */

    /**
     * @param AM_Controller_Action $oControllerAction
     * @param string $sComponentName Component name
     */
    public function __construct(AM_Controller_Action $oControllerAction, $sComponentName)
    {
        $this->_preInitialize();
        $this->setActionController($oControllerAction);
        $this->setName($sComponentName);
        $this->_initialize();
        $this->_postInitialize();
    }

    protected function _preInitialize()
    { }

    /**
     * Initialize component
     */
    protected function _initialize()
    {
        $this->_oDbAdapter = $this->getActionController()->oDb;
        $this->_oConfig    = $this->getActionController()->oConfig;
        $this->_oView      = $this->getActionController()->view;
        $this->_oLocalizer = $this->getActionController()->localizer;

        if ($this->getActionController()->getRequest()->isPost() && ($this->getRequestParam('form') == $this->getName())) {
            $this->_bIsSubmitted = true;
        }
    }

    /**
     * Postinitialize component
     */
    protected function _postInitialize()
    { }

    /**
     * Set action controller
     *
     * @param AM_Controller_Action $oControllerAction
     * @return AM_Component Provides a fluent interface
     */
    public function setActionController(Zend_Controller_Action $oControllerAction)
    {
        $this->_oControllerAction = $oControllerAction;

        return $this;
    }

    /**
     * Get action controller
     *
     * @return AM_Controller_Action
     */
    public function getActionController()
    {
        return $this->_oControllerAction;
    }

    /**
     * Return http equest param
     *
     * @param string $name Parameter name
     * @param string $default Default value
     */
    protected function getRequestParam($name, $default = null)
    {
        return $this->getActionController()->getRequest()->getParam($name, $default);
    }

    /**
     * Return name of component
     *
     * @return string
     */
    public function getName()
    {
        return $this->_sName;
    }

    /**
     * Set component name
     *
     * @param string $sName
     * @return AM_Component
     */
    public function setName($sName)
    {
        $this->_sName = (string) $sName;

        return $this;
    }

    /**
     * Return Errors list
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_aErrors;
    }
}
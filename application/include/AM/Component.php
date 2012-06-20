<?php
/**
 * @file
 * AM_Component class definition.
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
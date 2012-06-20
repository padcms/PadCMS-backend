<?php
/**
 * @file
 * AM_Controller_Action_Helper_Smarty class definition.
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
 * This class is responsible for Smarty template engine
 *
 * @todo: refactoring - implement Zend_View_Abstract (http://www.gediminasm.org/article/smarty-3-extension-for-zend-framework)
 *
 * @ingroup AM_Controller_Action_Helper
 * @ingroup AM_Controller_Action
 */
class AM_Controller_Action_Helper_Smarty extends Zend_Controller_Action_Helper_Abstract
{
    /** @var array Template parameters */
    private $_aParams = array(); /**< @type array */
    /** @var array Registred custom modifiers, functions, ... */
    private $_aCustomFunctions = array(); /**< @type array */
    /** @var array Smarty properties */
    private $_aSmartyProperties = array(); /**< @type array */
    /** @var string Name of smarty template for rendering if empty, then autocreate from current controller/action */
    public $_sResource; /**< @type string */
    /** @var array Array of additional response headers */
    public $aHeaders = array("Content-type" => "text/html; charset=utf-8");

    public function __construct(array $aProperties) {
        $this->_aSmartyProperties = $aProperties;
    }

    /**
     * Set template parameter
     *
     * @param string $sName Parameter name
     * @param mixed Parameter value
     */
    public function __set($sName, $mValue)
    {
        $this->_aParams[$sName] = $mValue;

        return $mValue;
    }

    /**
     * Return template parameter
     *
     * @param string $sName Parameter name
     * @return mixed Template parameter value, or null if not exists
     */
    public function __get($sName)
    {
        $mValue = array_key_exists($sName, $this->_aParams) ? $this->_aParams[$sName] : null;

        return $mValue;
    }

    /**
     * Checks that parameter has been set
     *
     * @param string $sName Parameter name
     * @return boolen
     */
    public function __isset($sName)
    {
        return array_key_exists($sName, $this->_aParams);
    }

    /**
     * @param type $sName
     */
    public function __unset($sName)
    {
        unset($this->_aParams[$sName]);
    }

    /**
     * Set manually resource(template name)
     *
     * @param string $sResource Name of resource
     */
    public function setResource($sResource)
    {
        $this->_sResource = $sResource;
    }

    /**
     * Really render
     */
    public function postDispatch()
    {
        $oSmarty = $this->getSmarty();
        $oSmarty->assign('baseUrl', $this->getFrontController()->getBaseUrl());

        $oSmarty->assign($this->_aParams);

        //determining template name
        $sTemplateName = $this->_sResource;
        if (!$sTemplateName) {
            if ($this->getRequest()->getControllerName() == 'index' && $this->getRequest()->getActionName() == 'index') {
                $sTemplateName = 'index.tpl';
            } else {
                $sTemplateName = strtolower($this->getRequest()->getControllerName()  . '/' . $this->getRequest()->getActionName()) . ".tpl";
            }
        }
        $this->getResponse()->setBody($oSmarty->fetch($sTemplateName));
        foreach($this->aHeaders as $sName => $mValue) {
            $this->getResponse()->setHeader($sName, $mValue, true);
        }
    }

    /**
     * Add custom function for later registering with smarty
     *
     * @param string $sType Type of function
     * @param callback $cCallback Function to registration
     * @param string $sName Name of modifier
     */
    public function addCustomFunction($sType, $cCallback, $sName = null)
    {
        if (array_key_exists($sType, $this->_aCustomFunctions)) {
            $this->_aCustomFunctions[$sType][] = array($cCallback, $sName);
        } else {
            $this->_aCustomFunctions[$sType] = array(array($cCallback, $sName));
        }
    }


    /**
     * setActionController()
     *
     * @param Zend_Controller_Action $oActionController
     * @return Zend_Controller_ActionHelper_Abstract
     */
    public function setActionController(Zend_Controller_Action $oActionController = null)
    {
        parent::setActionController($oActionController);

        $oActionController->view = $this;

        return $this;
    }

    /**
     * Return smarty instance
     *
     * @return Smarty
     */
    public function getSmarty()
    {
        static $oSmarty;

        if (!$oSmarty) {
            //create smarty object
            require_once 'Smarty.class.php';

            $oSmarty = new Smarty();
            foreach ($this->_aSmartyProperties as $sName => $mValue) {
                $oSmarty->$sName = substr($sName, - 4) == '_dir' ? Volcano_Tools::fixPath($mValue) : $mValue;
            }

            foreach ($this->_aCustomFunctions as $sType => $aItems) {
                foreach ($aItems as $aItem) {
                    if (substr($oSmarty->_version, 0, 1) != '2' ) { //bad code, but smarty3 has version like 'Smarty3-SVN$Rev: 3286 $'
                        if ($sType == 'modifier' || $sType == 'function') {
                            $oSmarty->registerPlugin($sType, $aItem[1], $aItem[0]);
                        } elseif ($sType == 'outputfilter') {
                            $oSmarty->registerFilter('output', $aItem[0]);
                        } elseif ($sType == 'postfilter') {
                            $oSmarty->registerFilter('post', $aItem[0]);
                        } elseif ($sType == 'prefilter') {
                            $oSmarty->registerFilter('pre', $aItem[0]);
                        }

                    } else {
                        if ($sType == 'modifier') {
                            $oSmarty->register_modifier($aItem[1], $aItem[0]);
                        } elseif ($sType == 'function') {
                            $oSmarty->register_function($aItem[1], $aItem[0]);
                        } elseif ($sType == 'outputfilter') {
                            $oSmarty->register_outputfilter($aItem[0]);
                        } elseif ($sType == 'postfilter') {
                            $oSmarty->register_postfilter($aItem[0]);
                        } elseif ($sType == 'prefilter') {
                            $oSmarty->register_prefilter($aItem[0]);
                        }
                    }
                }
            }
        }
        $oSmarty->error_reporting = error_reporting() & ~E_NOTICE;

        return $oSmarty;
    }
}
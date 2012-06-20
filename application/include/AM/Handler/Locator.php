<?php
/**
 * @file
 * AM_Handler_Locator class definition.
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
 * Handlers locator - find and create handler instance
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Locator implements AM_Handler_Locator_Interface
{
    const HANDLER_CLASS = 'AM_Handler_Abstract';

    /** @var AM_Handler_Locator */
    protected static $_oInstance = null; /**< @type AM_Handler_Locator */
    /** @var array Handlers classes stack */
    protected $_aHandlersClasses = array(); /**< @type array */
    /** @var array Handlers objects stack */
    protected $_aHandlersObjects = array(); /**< @type array */

    /**
     * @return AM_Handler_Locator
     */
    public static function getInstance()
    {
        if (is_null(self::$_oInstance)) {
            self::$_oInstance = new self();
        }

        return self::$_oInstance;
    }

    /**
     * Check handler
     * @param string | object $handler
     * @return boolean
     */
    private function _checkHandler($oHandler)
    {
        $reflectionHandler = new ReflectionClass($oHandler);

        return $reflectionHandler->isSubclassOf(self::HANDLER_CLASS);
    }

    /**
     * @see AM_Handler_Locator_Iterface::getHandler()
     * @param string $sName
     * @return AM_Handler_Abstract
     * @throws AM_Handler_Locator_Exception
     */
    public function getHandler($sName)
    {
        if (!array_key_exists($sName, $this->_aHandlersClasses)) {
            throw new AM_Handler_Locator_Exception(
                    sprintf('Handler with name "%s" not found', $sName),
                    501);
        }

        if (array_key_exists($sName, $this->_aHandlersObjects) && is_object($this->_aHandlersObjects[$sName])) {
            return $this->_aHandlersObjects[$sName];
        }

        $sHandlerClass  = $this->_aHandlersClasses[$sName];
        $oHandlerObject = new $sHandlerClass();

        $this->_aHandlersObjects[$sName] = $oHandlerObject;

        return $oHandlerObject;
    }

    /**
     * @see AM_Handler_Locator_Iterface::setHandler()
     * @param string $sName
     * @param string | object $handler
     * @return AM_Handler_Locator
     * @throws AM_Handler_Locator_Exception
     */
    public function setHandler($sName, $mHandler)
    {
        if (!$this->_checkHandler($mHandler)) {
            $sClassName = is_object($mHandler)? get_class($mHandler) : $mHandler;
            throw new AM_Handler_Locator_Exception(
                    sprintf('Service "%s" must extends "%s". But instance of "%s" given',
                            $sName, self::HANDLER_CLASS, $sClassName),
                    502);
        }

        if (is_object($mHandler)) {
            $this->_aHandlersObjects[$sName] = $mHandler;
            $this->_aHandlersClasses[$sName] = get_class($mHandler);

            return $this;
        }

        $this->_aHandlersClasses[$sName] = $mHandler;
        $this->_aHandlersObjects[$sName] = null;

        return $this;
    }
}
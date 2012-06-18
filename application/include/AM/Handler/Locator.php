<?php
/**
 * @file
 * AM_Handler_Locator class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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
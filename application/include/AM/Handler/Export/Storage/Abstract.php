<?php
/**
 * @file
 * AM_Handler_Export_Storage_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Abstract class: the base class of revision package storages
 * This class is responsible for keeping the package
 *
 * @ingroup AM_Handler
 */
abstract class AM_Handler_Export_Storage_Abstract implements AM_Handler_Export_Storage_Interface
{
    /** @var AM_Handler_Export_Package_Abstract **/
    protected $_oPackage = null; /**< @type AM_Handler_Export_Package_Abstract */
    /** @var AM_Handler_Abstract **/
    protected $_oHandler = null; /**< @type AM_Handler_Abstract */
    /** @var string The path prefix (usually this is a path like 00/00/00/01, where 1 - revision id) **/
    protected $_sPathPrefix = null; /**< @type string */

    public function __construct(AM_Handler_Abstract $oHandler)
    {
        $this->setHandler($oHandler);
    }

    /**
     * Add package to the storage
     * @param AM_Handler_Export_Package_Abstract $oPackage
     * @return AM_Handler_Export_Storage_Abstract
     * @throws AM_Handler_Export_Storage_Exception
     */
    public final function addPackage(AM_Handler_Export_Package_Abstract $oPackage)
    {
        $this->_oPackage = $oPackage;

        return $this;
    }

    /**
     * Get package
     * @return AM_Handler_Export_Package_Abstract
     */
    public function getPackage()
    {
        return $this->_oPackage;
    }

    /**
     * Set handler
     * @param AM_Handler_Abstract $oHandler
     * @return AM_Handler_Export_Storage_Abstract
     */
    public function setHandler(AM_Handler_Abstract $oHandler)
    {
        $this->_oHandler = $oHandler;

        return $this;
    }

    /**
     * Return handler
     * @return AM_Handler_Abstract
     */
    public function getHandler()
    {
        return $this->_oHandler;
    }

    /**
     * Set path prefix (usually path 00/00/00/01)
     * @param string $sPathPrefix
     * @return AM_Handler_Export_Storage_Abstract
     */
    public function setPathPrefix($sPathPrefix)
    {
        $this->_sPathPrefix = $sPathPrefix;

        return $this;
    }
}
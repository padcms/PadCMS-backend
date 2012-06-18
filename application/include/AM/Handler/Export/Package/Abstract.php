<?php
/**
 * @file
 * AM_Handler_Export_Package_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Abstract class: the base class of revision package
 *
 * @ingroup AM_Handler
 */
abstract class AM_Handler_Export_Package_Abstract implements AM_Handler_Export_Package_Interface
{
    /** @var string The name of the package. This name used to save package to disk or other place */
    protected $_sPackageName         = null; /**< @type string */
    /** @var string The name of downloaded package */
    protected $_sPackageDownloadName = null; /**< @type string */
    /** @var string The package path */
    protected $_sPackagePath         = null; /**< @type string */
    /** @var array The files inside the package */
    protected $_aFiles               = array(); /**< @type array */
    /** @var AM_Handler_Abstract **/
    protected $_oHandler             = null;

    /**
     * @param AM_Handler_Abstract $oHandler
     * @param string $sName The name of package
     */
    public function __construct(AM_Handler_Abstract $oHandler, $sName = null)
    {
        $this->setHandler($oHandler);

        if (!is_null($sName)) {
            $this->setPackageName($sName);
        }
    }

    /**
     * Set handler
     *
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
     *
     * @return AM_Handler_Abstract
     */
    public function getHandler()
    {
        return $this->_oHandler;
    }

    /**
     * Add file resource to the package
     *
     * @see AM_Handler_Export_Package_Interface::addFile()
     * @param string $sPathReal
     * @param string $sPathInPackage
     * @return AM_Handler_Export_Package_Abstract
     * @throws AM_Handler_Export_Package_Exception
     */
    public function addFile($sPathReal, $sPathInPackage)
    {
        if (!AM_Tools_Standard::getInstance()->file_exists($sPathReal)) {
            throw new AM_Handler_Export_Package_Exception(sprintf('File "%s" doesn\'t exist', $sPathReal));
        }

        if (empty($sPathInPackage)) {
            throw new AM_Handler_Export_Package_Exception('File path in package have to be not empty');
        }

        $this->_aFiles[$sPathReal] = $sPathInPackage;

        return $this;
    }

    /**
     * @see AM_Handler_Export_Package_Interface::getPackagePath()
     * @return string|null
     */
    public function getPackagePath()
    {
        return $this->_sPackagePath;
    }

    /**
     * @see AM_Handler_Export_Package_Interface::setPackageName()
     * @param string $sName
     * @return AM_Handler_Export_Package_Abstract
     */
    public function setPackageName($sName)
    {
        $this->_sPackageName = $sName;

        return $this;
    }

    /**
     * @see AM_Handler_Export_Package_Interface::setPackageDownloadName()
     * @param string $sName
     * @return AM_Handler_Export_Package_Abstract
     */
    public function setPackageDownloadName($sName)
    {
        $this->_sPackageDownloadName = $sName;

        return $this;
    }

    /**
     * Remove files from package stack
     * @return AM_Handler_Export_Package_Abstract
     */
    public function reset()
    {
        $this->_aFiles = array();

        return $this;
    }
}
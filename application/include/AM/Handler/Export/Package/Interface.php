<?php
/**
 * @file
 * AM_Handler_Export_Package_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Handler
 * @ingroup AM_Exception
 */
interface AM_Handler_Export_Package_Interface
{
    public function setHandler(AM_Handler_Abstract $oHandler);

    public function getHandler();

    /**
     * Add file resource to the package
     * @param string $sPathReal Path to resource in the file system
     * @param string $sPathInPackage Relative path of file in package
     */
    public function addFile($sPathReal, $sPathInPackage);

    /**
     * Save package to disk
     */
    public function savePackage();

    /**
     * Set package name
     * @param string $sName
     */
    public function setPackageName($sName);

    /**
     * Set package download name (Need for suuport old versions of app)
     */
    public function getPackageDownloadName();

    /**
     * Set package download name (Need for suuport old versions of app)
     * @param string $sName
     */
    public function setPackageDownloadName($sName);

    /**
     * Get package name
     */
    public function getPackageName();

    /**
     * @return string|null Returns the package full path after save, or null
     * if package wasn't saved
     */
    public function getPackagePath();
}
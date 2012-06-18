<?php
/**
 * @file
 * AM_Handler_Export_Storage_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The package storage interface
 * @ingroup AM_Handler
 */
interface AM_Handler_Export_Storage_Interface
{
    /**
     * Add package to the storage
     * @param AM_Handler_Export_Package_Abstract $oPackage
     */
    public function addPackage(AM_Handler_Export_Package_Abstract $oPackage);

    /**
     * Save package in the storage
     */
    public function savePackage();

    /**
     * Send package content to the STDOUT
     */
    public function sendPackage();
}
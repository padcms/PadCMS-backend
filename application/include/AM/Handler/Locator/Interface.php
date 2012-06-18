<?php
/**
 * @file
 * AM_Handler_Locator_Interface class definition.
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
 */
interface AM_Handler_Locator_Interface
{
    /**
     * Retrun locator instance
     */
    public static function getInstance();

    /**
     * Search handler by name and return its instance
     */
    public function getHandler($sName);

    /**
     * Set handler by name (by handler object or class name and handler name)
     */
    public function setHandler($sName, $mHandler);
}
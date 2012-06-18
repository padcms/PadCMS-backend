<?php
/**
 * @file
 * AM_Handler_Export_Storage_Mock class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The storage stub
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Export_Storage_Mock extends AM_Handler_Export_Storage_Abstract
{
    public function savePackage()
    { }

    public function sendPackage($mIsContinue = null)
    {
        echo "Package content";
    }
}
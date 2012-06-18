<?php
/**
 * @file
 * AM_Resource_Processor_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Resource
 */
interface AM_Resource_Processor_Interface
{
    /**
     * Resize image
     */
    public function resizeImage($sSrc, $sDst, $iWidth, $iHeight, $sMode);
}
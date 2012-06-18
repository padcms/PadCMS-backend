<?php
/**
 * @file
 * AM_Resource_Processor class definition.
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
class AM_Resource_Processor implements AM_Resource_Processor_Interface
{
    /**
     * @param string $sSrc
     * @param string $sDst
     * @param int $iWidth
     * @param int $iHeight
     * @param string $sMode
     */
    public function resizeImage($sSrc, $sDst, $iWidth, $iHeight, $sMode)
    {
        AM_Tools_Image::resizeImage($sSrc, $sDst, $iWidth, $iHeight, $sMode);
    }
}
<?php
/**
 * @file
 * AM_Handler_Export_Package_Mock class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The package stub
 * @ingroup AM_Handler
 */
class AM_Handler_Export_Package_Mock extends AM_Handler_Export_Package_Abstract
{
    /**
     * @return AM_Handler_Export_Package_Mock
     */
    public function savePackage()
    {
        return $this;
    }

    public function addFile($sPathReal, $sPathInPackage)
    {
        $this->_aFiles[$sPathReal] = $sPathInPackage;

        return $this;
    }

    public function getPackagePath()
    {
        return 'mock_package.zip';
    }

    public function getPackageName()
    {
        return 'mock_package.zip';
    }

    public function getPackageDownloadName()
    {
        return 'mock_package.zip';
    }
}
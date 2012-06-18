<?php
/**
 * @file
 * AM_Handler_Export_Package_Zip class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The ZIP implemenation of the package
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Export_Package_Zip extends AM_Handler_Export_Package_Abstract
{
    /**
     * Save zip to the file system
     * @return AM_Handler_Export_Package_Zip
     * @throws AM_Handler_Export_Package_Exception
     */
    public function savePackage()
    {
        //Create temp file for zip
        $sTempZipFile = AM_Handler_Temp::getInstance()->getFile($this->getPackageName());

        $oZip = new ZipArchive();
        $rArchiveResource = $oZip->open($sTempZipFile, ZIPARCHIVE::CREATE);
        if ($rArchiveResource !== true) {
            throw new AM_Handler_Export_Package_Exception('I/O error. Can\'t create zip file: ' . $sTempZipFile);
        }
        //Add files to archive
        foreach ($this->_aFiles as $sPathReal => $sPathInPakage) {
            $oZip->addFile($sPathReal, $sPathInPakage);
        }

        $oZip->close();

        $this->_sPackagePath = $sTempZipFile;

        return $this;
    }

    /**
     * @see AM_Handler_Export_Package_Interface::getPackageName()
     * @return string
     */
    public function getPackageName()
    {
        return $this->_sPackageName . '.zip';
    }

    /**
     * @see AM_Handler_Export_Package_Interface::getPackageDownloadName()
     * @return string
     */
    public function getPackageDownloadName()
    {
        return $this->_sPackageDownloadName . '.zip';
    }
}
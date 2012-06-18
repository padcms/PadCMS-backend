<?php
/**
 * @file
 * AM_Handler_Export_Storage_Local class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The implementation of package storage. This class is responsible for keeping the package on local disc storage
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Export_Storage_Local extends AM_Handler_Export_Storage_Abstract
{
    /**
     * Save package to the local storage
     *
     * @return AM_Handler_Export_Storage_Local
     * @throws AM_Handler_Export_Storage_Exception
     */
    public function savePackage()
    {
        if (is_null($this->_oPackage)) {
            throw new AM_Handler_Export_Storage_Exception('Package is empty');
        }

        $this->_oPackage->savePackage();

        $sPathTmp = $this->_oPackage->getPackagePath();
        //Check that package exists
        if (!AM_Tools_Standard::getInstance()->file_exists($sPathTmp)) {
            throw new AM_Handler_Export_Storage_Exception(sprintf('File "%s" doesn\'t exist', $sPathTmp));
        }

        //Prepare path on the local storage
        $sPackageFileName = pathinfo($sPathTmp, PATHINFO_BASENAME);
        $sPathToSave      = $this->_buildPackagePath();

        if (!AM_Tools_Standard::getInstance()->is_dir($sPathToSave)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sPathToSave, 0777, true)) {
                throw new AM_Handler_Export_Storage_Exception(sprintf('I/O error while create dir "%s"', $sPathToSave));
            }
        }

        $sPathToSave .= DIRECTORY_SEPARATOR . $sPackageFileName;

        //Copy file to the local storage
        if (!AM_Tools_Standard::getInstance()->copy($sPathTmp, $sPathToSave)) {
            throw new AM_Handler_Export_Storage_Exception(sprintf('I/O error while copy files from "%s" to "%s"', $sPathTmp, $sPathToSave));
        }

        AM_Tools_Standard::getInstance()->chmod($sPathToSave, 0666);

        $this->getHandler()->getLogger()->debug(sprintf('Package "%s" saved to "%s"', $sPathTmp, $sPathToSave));

        return $this;
    }

    /**
     * @see AM_Handler_Export_Storage_Interface::sendPackage()
     * @throws AM_Handler_Export_Storage_Exception
     */
    public function sendPackage()
    {
        $sFilePath = $this->_buildPackagePath() . DIRECTORY_SEPARATOR . $this->getPackage()->getPackageName();
        $sFileName = $this->getPackage()->getPackageDownloadName();

        if (!file_exists($sFilePath)) {
            throw new AM_Handler_Export_Storage_Exception(sprintf('File "%s" not found', $sFilePath));
        }

        $oResponse = new Zend_Controller_Response_Http();
        $oResponse->setHttpResponseCode(200);

        $oRequest  = new Zend_Controller_Request_Http();

        $iFileSize  = filesize($sFilePath);
        $sFileMtime = @gmdate("D, d M Y H:i:s", @filemtime($sFilePath)) . " GMT";
        $rFile      = @fopen($sFilePath, 'rb');

        $sRange = $oRequest->get('HTTP_RANGE');

        //Trying to resume download according to the HTTP_RANGE header
        if (preg_match('/bytes=(\d+)-(\d*)/i', $sRange, $matches)) {
            $sRange = $matches[1];
        } else {
            $sRange = false;
        }

        if ($sRange) {
            fseek($rFile, $sRange);
            $oResponse->setHttpResponseCode(206);
            $oResponse->setHeader('Content-Range', sprintf('bytes %d-%d/%d', $sRange, $iFileSize - 1, $iFileSize));
        }

        $oResponse->setHeader('Content-Disposition', 'attachment; filename=' . $sFileName)
                ->setHeader('Content-Length', ($iFileSize - $sRange))
                ->setHeader('Content-Type', 'application/octet-stream')
                ->setHeader('Accept-Ranges', 'bytes')
                ->setHeader('Last-Modified', $sFileMtime);

        while (!feof($rFile)) {
            $sBuffer = fread($rFile, 2048);
            $oResponse->appendBody($sBuffer);
        }
        fclose($rFile);

        $oResponse->sendResponse();
    }

    /**
     * Build package path
     * @return string
     */
    protected function _buildPackagePath()
    {
        $sPath = rtrim($this->getHandler()->getConfig()->content->export, DIRECTORY_SEPARATOR);

        if (!empty($this->_sPathPrefix)) {
            $sPath .= DIRECTORY_SEPARATOR . trim($this->_sPathPrefix, DIRECTORY_SEPARATOR);
        }

        return $sPath;
    }
}
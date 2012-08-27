<?php
/**
 * @file
 * AM_Tools_Image class definition.
 *
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Tools
 */

/**
 * @ingroup AM_Tools
 */
class AM_Tools_Image
{
    const TILE_SIZE_RETINA = 1024;
    const TILE_SIZE        = 512;

    const TILE_BAD_QUALITY_SIZE_PROPORTION        = 25;
    const TILE_BAD_QUALITY_SIZE_PROPORTION_RETINA = 7;

    /**
     * Returns confgi instance
     * @return Zend_Config
     */
    public static function getConfig()
    {
        return Zend_Registry::get('config');
    }

    /**
     * Resize image and save
     * @param string $sPathSrc Path to source image
     * @param string $sPathDst Path to output image
     * @param integer $iWidth Width of output image
     * @param integer $iHeight Height of output image
     * @param string $sMode Transformation mode
     * @param string $sImageForZoomPath Path for zoom thumbnail
     */
    public static function resizeImage($sPathSrc, $sPathDst, $iWidth, $iHeight, $sMode = "out", $sImageForZoomPath = null)
    {
        if (empty($sPathDst)) {
          $sPathDst = $sPathSrc;
        }

        if ($sMode == 'noresize') {
            return AM_Tools_Standard::getInstance()->copy($sPathSrc, $sPathDst);
        }

        $oSrcImage  = new Imagick($sPathSrc);
        $iSrcWidth  = $oSrcImage->getimagewidth();
        $iSrcHeight = $oSrcImage->getimageheight();

        //For images with horizontal proportions in vertical issue
        if ($iSrcWidth > $iSrcHeight && $iWidth < $iHeight && $sMode == 'width') {
            $iTmpHeight = $iHeight;
            $iHeight    = $iWidth;
            $iWidth     = $iTmpHeight;
        }

        self::_resizeImage($sPathSrc, $sPathDst, $iWidth, $iHeight, $sMode);

        if (!is_null($sImageForZoomPath)) {
            self::_resizeImage($sPathSrc, $sImageForZoomPath, $iWidth*2, $iHeight*2, $sMode);
        }
    }

    /**
     * Resize image and save
     * @todo rename
     * @param string $sPathSrc Path to source image
     * @param string $sPathDst Path to output image
     * @param integer $iWidth Width of output image
     * @param integer $iHeight Height of output image
     * @param string $sMode Transformation mode
     */
    protected static function _resizeImage($sPathSrc, $sPathDst, $iWidth, $iHeight, $sMode = "out")
    {
        $sCmd = null;

        switch ($sMode) {
            case "width":
                $sCmd = sprintf('nice -n 15 %s %s -resize %dx %s', self::getConfig()->bin->convert, $sPathSrc, $iWidth, $sPathDst);
                break;
            case "height":
                $sCmd = sprintf('nice -n 15 %s %s -resize x%d %s', self::getConfig()->bin->convert, $sPathSrc, $iHeight, $sPathDst);
                break;
            case "out":
            default:
                $sCmd = sprintf('nice -n 15 %1$s %2$s -resize %3$dx -crop %3$dx%4$d+0+0 %5$s', self::getConfig()->bin->convert, $sPathSrc, $iWidth, $iHeight, $sPathDst);
                break;
        }

        AM_Tools_Standard::getInstance()->passthru($sCmd);
    }

    /**
     * http://www.imagemagick.org/Usage/crop/#crop_equal
     * @param string $sImageOriginal - the original image with high resolution
     * @param string $sImageThumbnail - the thumbnail given from original image
     * @param string $sArchivePath - path of the archive with cropped image
     * @param integer $iBlockSize - the tile size (size of the square to crop)
     * @param string $sImageForZoomPath Path for zoom thumbnail
     * @return void
     * @throws AM_Exception
     */
    public static function cropImage($sImageOriginal, $sImageThumbnail, $sArchivePath, $iBlockSize = self::TILE_SIZE, $sImageForZoomPath = null)
    {
        $sTempDir = AM_Handler_Temp::getInstance()->getDir();
        $aFiles   = self::_cropImage($sImageThumbnail, $sTempDir, $iBlockSize);

        $oZip             = new ZipArchive();
        $rArchiveResource = $oZip->open($sArchivePath, ZIPARCHIVE::CREATE);

        if ($rArchiveResource !== true) {
            throw new AM_Exception('I/O error. Can\'t create zip file: ' . $sZipPath);
        }

        if (!is_null($sImageForZoomPath)) {
            $aFilesZoom = self::_cropImage($sImageForZoomPath, $sTempDir, $iBlockSize, '_2x');

            $aFiles = array_merge($aFiles, $aFilesZoom);
        }

        foreach ($aFiles as $sFile) {
            //Optimization
            if ('png' == pathinfo($sFile, PATHINFO_EXTENSION)) {
                self::optimizePng($sFile);
            }
            $oZip->addFile($sFile, pathinfo($sFile, PATHINFO_BASENAME));
        }


        $oZip->close();
    }

    /**
     * @todo rename
     * @param string $sImage
     * @param string $sOutputFolder
     * @param int $iBlockSize
     * @return array - array of cropped images
     */
    protected static function _cropImage($sImage, $sOutputFolder, $iBlockSize, $sPathPostFix = '')
    {
        $aFileInfo = pathinfo($sImage);
        $sCmd = sprintf('nice -n 15 %1$s %2$s -crop %4$dx%4$d -set filename:title "%%[fx:page.y/%4$d+1]_%%[fx:page.x/%4$d+1]" +repage  +adjoin %3$s/"resource_%%[filename:title]%6$s.%5$s"', self::getConfig()->bin->convert, $sImage, $sOutputFolder, $iBlockSize, $aFileInfo['extension'], $sPathPostFix);
        AM_Tools_Standard::getInstance()->passthru($sCmd);

        $aFiles = AM_Tools_Finder::type('file')
                ->name('resource_*' . $sPathPostFix . '.' . $aFileInfo['extension'])
                ->sort_by_name()
                ->in($sOutputFolder);

        return $aFiles;
    }

    /**
     * Optimize png image with optipng tool
     * @param string $sFilePath
     */
    public static function optimizePng($sFilePath)
    {
        $sOptipngPath = Zend_Registry::get('config')->bin->optipng;
        $sCmd         = sprintf('nice -n 15 %s -zc9 -zm8 -zs0 -f5 %s > /dev/null 2>&1', $sOptipngPath, $sFilePath);
        AM_Tools_Standard::getInstance()->passthru($sCmd);
    }
}
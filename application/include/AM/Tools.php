<?php
/**
 * @file
 * AM_Tools class definition.
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
class AM_Tools extends Volcano_Tools
{
    protected static $_allowedImageExtensoins = array('gif', 'png', 'jpg', 'jpeg', 'pdf', 'pdf');

    /**
     * Returns timestamp in human friendly format (2 weeks ago, etc.)
     *
     * @todo Move to the separate helper
     * @param int $iTimeStamp
     * @param string $sPrefix
     * @return string
     */
    public static function getFriendlyDate($iTimeStamp, $sPrefix = 'Posted')
    {
        $fDiffInDays = (time() - $iTimeStamp) / 86400 ;
        if ($fDiffInDays >= 8) {
            return $sPrefix . ' More Than Week Ago at '.date('h:i A', $iTimeStamp);
        }
        if ($fDiffInDays >= 7 && $fDiffInDays < 8) {
            return $sPrefix . ' Week Ago at '.date('h:i A', $iTimeStamp);
        }
        if ($fDiffInDays >= 1 && $fDiffInDays < 2) {
            return $sPrefix . ' Yesterday  at '.date('h:i A', $iTimeStamp);
        }
        if ($fDiffInDays < 1) {
            $fDiffInHours = (time() - $iTimeStamp) / 3600;
            return $sPrefix . ' Today at '.date('h:i A', $iTimeStamp); //intval($hdiff)." Hours Ago";
        }
        return $sPrefix . ' '. intval($fDiffInDays)." Days Ago at ".date('h:i A', $iTimeStamp);
    }

    /**
     * Removes resized resource(s)
     *
     * @param string $sResourceType The type of resource (element, toc, static-pdf, etc.)
     * @param string $sPresetGroup The name of presets group (it usualy trhe same as resource type, except elements- element-horizontal, element-vertical, etc)
     * @param int $iId
     * @param string $sFileName
     * @return void
     */
    public static function clearResizerCache($sResourceType, $sPresetGroup = null, $iId = null, $sFileName = null)
    {
        $oThumbnailerHandler = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
        /* @var $oThumbnailerHandler AM_Handler_Thumbnail */
        $oThumbnailerHandler->clearThumbnails($sResourceType, $sPresetGroup, $iId, $sFileName);
    }

    /**
     * Removes content files
     *
     * @todo Refactor and move to the separate helper
     * @param string $sType The type of resource (element, toc, static-pdf, etc.)
     * @param int $iId
     * @param string $sFileName
     * @return void
     */
    public static function clearContent($sType = null, $iId = null, $sFileName = null)
    {
        $sBasePath = Zend_Registry::get('config')->content->base;

        $sSplittedIdPath = is_null($iId) ? null : trim(AM_Tools_String::generatePathFromId($iId), DIRECTORY_SEPARATOR);

        if (!empty($sType)) {
            $sBasePath .= DIRECTORY_SEPARATOR . $sType;
        }

        if (!is_null($sSplittedIdPath)) {
            $sBasePath .= DIRECTORY_SEPARATOR . $sSplittedIdPath;
        }

        if (!empty($sFileName)) {
            $aFiles = glob($sBasePath . '/' . $sFileName);
            if ($aFiles) {
                foreach ($aFiles as $sFile) {
                    AM_Tools_Standard::getInstance()->unlink($sFile);
                }
            }
            return;
        }

        if (!AM_Tools_Standard::getInstance()->is_dir($sBasePath)) {
            return;
        }

        AM_Tools_Standard::getInstance()->clearDir($sBasePath);
    }

    /**
     * Returns path to the resource with sType type
     *
     * @todo Refactor and move to the separate helper
     * @param string $sType The type of resource (element, toc, static-pdf, etc.)
     * @param integer $iId
     * @return string
     */
    public static function getContentPath($sType, $iId = null)
    {
        $sPath = Zend_Registry::get('config')->content->base;
        $sPath .= DIRECTORY_SEPARATOR . $sType;
        if (!is_null($iId)) {
            $sPath .= DIRECTORY_SEPARATOR . trim(AM_Tools_String::generatePathFromId($iId), DIRECTORY_SEPARATOR);
        }

        return $sPath;
    }

    /**
     * Returns path to the resized resource
     *
     * @todo Refactor and move to the separate helper
     * @param string $sPreset The name of resizing preset
     * @param string $sType The type of resource (element, toc, static-pdf, etc.)
     * @param int $iId
     * @param string $sFileName
     * @return string
     */
    public static function getImageUrl($sPreset, $sType, $iId = null, $sFileName = null, $sFileExtension = null)
    {
        $oThumbnailerHandler = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
        /* @var $oThumbnailerHandler AM_Handler_Thumbnail */

        $sImageUrl = $oThumbnailerHandler->getResourceUrl($sPreset, $sType, $iId, $sFileName, $sFileExtension);

        return $sImageUrl;
    }

    /**
     * Checks is the file has image extension
     *
     * @todo Refactor and move to the separate helper
     * @param string $sFileName The file name or path
     * @return boolean
     */
    public static function isAllowedImageExtension($sFileName)
    {
        $sFileExtension = strtolower(pathinfo($sFileName,PATHINFO_EXTENSION));
        $bResult        = in_array($sFileExtension, self::$_allowedImageExtensoins);

        return $bResult;
    }

    /**
     * Returns path to the icon for non image file
     *
     * @todo Refactor and move to the separate helper
     * @param string $sFileName
     * @return string Image URI
     */
    public static function getIconForNonImageFile($sFileName) {
        $pathInfo = pathinfo($sFileName);
        $ext = $pathInfo['extension'];
        switch ($ext) {
            case 'pdf': return '/images/map/b-pic.png';
            case 'zip': return '/images/map/c-pic.png';
            default:
                return '/images/map/d-pic.png';
        }
    }

    /**
     * Strips tags
     *
     * @todo Refactor and move to the separate helper
     * @param string $sText
     * @return string
     */
    public static function filter_xss($sText)
    {
        $aAllowedTags       = array();
        $aAllowedAttributes = array();
        $oFfilter = new Zend_Filter_StripTags(array('allowTags'  => $aAllowedTags,
                                                  'allowAttribs' => $aAllowedAttributes));

        $sText = trim($sText);
        $sText = $oFfilter->filter($sText);

        return $sText;
    }
}

<?php
/**
 * @file
 * AM_Handler_Thumbnail class definition.
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
 * Temp handler - creates temporary files in system temp dir, deletes files/dirs in destructor
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Thumbnail extends AM_Handler_Abstract implements AM_Handler_Thumbnail_Interface
{
    /** @var array */
    protected $_aSources = array(); /**< @type array */
    /** @var array */
    protected $_aPresets = array(); /**< @type array */
    /** @var array List of created thumbnail **/
    protected $_aThumbnails = array(); /**< @type array */
    /** @var AM_Resource_Processor **/
    protected $_oResourceProcessor = null; /**< @type AM_Resource_Processor */

    /**
     * @param array | null $aFiles array of image files
     * @param array | null $aPresets  array of presets
     */
    public function __construct($aFiles = null, $aPresets = null)
    {
        if (!is_null($aFiles)) {
            foreach ((array) $aFiles as $file) {
                $this->addFile($file);
            }
        }

        if (!is_null($aPresets)) {
            foreach ((array) $aPresets as $preset) {
                $this->addPreset($preset);
            }
        }
    }

    /**
     * Get resource processor instance
     * @return AM_Resource_Processor
     */
    public function getResourceProcessor()
    {
        if (is_null($this->_oResourceProcessor)) {
            $this->_oResourceProcessor = new AM_Resource_Processor();
        }

        return $this->_oResourceProcessor;
    }

    /**
     * Get configuration for thumbnailer
     * @return Zend_Config
     */
    public function getConfig()
    {
        if (is_null($this->_oConfig)) {
            $this->_oConfig = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'thumbnail.ini');
        }

        return $this->_oConfig;
    }

    /**
     * Add sorce file to thumbnailer
     * @param string $sFilePath path to image file
     * @return AM_Handler_Thumbnail
     * @throws AM_Handler_Thumbnail_Exception
     */
    public function addSourceFile($sFilePath)
    {
        $oSource = AM_Resource_Factory::create((string) $sFilePath);

        if (!is_object($oSource) || !$oSource instanceof AM_Resource_Abstract) {
            throw new AM_Handler_Thumbnail_Exception(sprintf('Wrong source given', 501));
        }

        $this->_aSources[] = $oSource;

        return $this;
    }

    /**
     * Get list of sources
     *
     * @return array
     */
    public function getSources()
    {
        return $this->_aSources;
    }

    /**
     * Add preset on wich resize image
     * @param string $sPreset
     * @return AM_Handler_Thumbnail
     * @throws AM_Handler_Thumbnail_Exception
     */
    public function addPreset($sPreset)
    {
        $sPreset = (string) $sPreset;
        if (!isset($this->getConfig()->{$sPreset})) {
            throw new AM_Handler_Thumbnail_Exception(sprintf('There is not preset with name \'%s\' in the config', $sPreset), 502);
        }

        $this->_aPresets[] = $sPreset;

        return $this;
    }

    /**
     * Remove all presets from stack
     *
     * @return AM_Handler_Thumbnail
     */
    public function clearPresets()
    {
        $this->_aPresets = array();

        return $this;
    }

    /**
     * Remove all source files from stack
     *
     * @return AM_Handler_Thumbnail
     */
    public function clearSources()
    {
        $this->_aSources = array();

        return $this;
    }

    /**
     * Get default thumbnail url for preset
     * @param string $sPreset
     * @return string
     * @throws AM_Handler_Thumbnail_Exception
     */
    public function getPresetDefaultThumbnailUrl($sPreset)
    {
        $sPreset = (string) $sPreset;
        if (!isset($this->getConfig()->{$sPreset})) {
            throw new AM_Handler_Thumbnail_Exception(sprintf('There are not presets with the name \'%s\' in the config', $sPreset), 502);
        }

        $sThumbnailFile = $this->getConfig()->{$sPreset}->get('default', $this->getConfig()->common->defaultThumbnail);

        $sThumbnailUrl = $this->getConfig()->common->defaultThumbnailUrl . '/' . $sThumbnailFile;

        return $sThumbnailUrl;
    }

    /**
     * Create thumbnails for each source
     * @return AM_Handler_Thumbnail
     */
    public function createThumbnails()
    {
        $sOutputFolder = $this->getThumbnailsPath();

        foreach ($this->_aSources as $oSource) {
            /* @var $oSource AM_Resource_Abstract */
            $sInputFile  = $oSource->getFileForThumbnail();
            foreach ($this->_aPresets as $sPreset) {
                if (!isset($this->getConfig()->{$sPreset})) {
                    continue;
                }

                $oPresetConfig = $this->getConfig()->{$sPreset};
                //Get path without 'sourceFolder'
                $sSourceFilePath = substr($oSource->getSourceFileDir(), strlen($this->getConfig()->common->sourceFolder));
                $sOutputFilePath = $sOutputFolder
                        . DIRECTORY_SEPARATOR
                        . $sPreset
                        . DIRECTORY_SEPARATOR
                        . trim($sSourceFilePath, DIRECTORY_SEPARATOR);
                AM_Tools_Standard::getInstance()->mkdir($sOutputFilePath, octdec($this->getConfig()->common->thumbnailDirChmod), true);
                $sOutputFile = $sOutputFilePath
                        . DIRECTORY_SEPARATOR
                        . $oSource->getSourceFileName()
                        . '.'
                        . pathinfo($sInputFile, PATHINFO_EXTENSION);

                if ($oSource->isImage()) {
                    $this->getResourceProcessor()->resizeImage($sInputFile, $sOutputFile, $oPresetConfig->width, $oPresetConfig->height, $oPresetConfig->method);
                } else {
                    //We have to copy not image resource only to the 'none' preset folder
                    if ($sPreset == 'none') {
                        AM_Tools_Standard::getInstance()->copy($sInputFile, $sOutputFile);
                    }
                }

                AM_Tools_Standard::getInstance()->chmod($sOutputFile, octdec($this->getConfig()->common->thumbnailFileChmod));
                $this->_aThumbnails[] = $sOutputFile;
            }
        }

        return $this;
    }

    /**
     * Returns folder path where thumbnails are
     *
     * @return type
     */
    public function getThumbnailsPath()
    {
        $sPath = $this->getConfig()->common->thumbnailFolder;

        return $sPath;
    }

    /**
     * Returns folder path where thumbnails are
     *
     * @return type
     */
    public function getThumbnailsUri()
    {
        $sPath = $this->getConfig()->common->thumbnailUrl;

        return $sPath;
    }

    /**
     * Return list of created thumbnail
     * @return array
     */
    public function getThumbnails()
    {
        return $this->_aThumbnails;
    }

    /**
     * Load all presets for given resource type
     *
     * @param string|null $sResourceType
     * @return AM_Handler_Thumbnail
     */
    public function loadAllPresets($sResourceType = null)
    {
        $this->clearPresets();
        $oResourcesConfig = $this->getConfig()->common->resource;

        $aPresets = $oResourcesConfig->default->toArray();
        if (!is_null($sResourceType)) {
            if (isset($oResourcesConfig->{$sResourceType})) {
                $aPresets = array_merge($aPresets, $oResourcesConfig->{$sResourceType}->toArray());
            }
        }

        $this->_aPresets = $aPresets;

        return $this;
    }

    /**
     * Get resource's resolutions from config
     *
     * @param string $sResourceType
     * @return array
     */
    public function getResolutions($sResourceType)
    {
        $oResourceConfigarations = $this->getConfig()->common->resource;
        $aResult = array();
        if (isset($oResourceConfigarations->{$sResourceType})) {
            foreach ($oResourceConfigarations->{$sResourceType} as $sPreset) {
                if (preg_match('/^\d+-\d+/', $sPreset)) {
                    $aResult[] = $sPreset;
                }
            }
        }

        return $aResult;
    }

    public function clearThumbnails($sType, $iId = null, $sFileName = null)
    {
        $this->loadAllPresets($sType);

        $sSplittedIdPath = is_null($iId) ? null : trim(AM_Tools_String::generatePathFromId($iId), DIRECTORY_SEPARATOR);

        foreach ($this->_aPresets as &$sValue) {
            $sPath = $this->getThumbnailsPath() . DIRECTORY_SEPARATOR . $sValue;

            if (!empty($sType)) {
                $sPath .= DIRECTORY_SEPARATOR . $sType;
            }

            if (!is_null($sSplittedIdPath)) {
                $sPath .= DIRECTORY_SEPARATOR . $sSplittedIdPath;
            }

            if (!empty($sFileName)) {
                $aFiles = glob($sPath . DIRECTORY_SEPARATOR . $sFileName);
                if ($aFiles) {
                    foreach ($aFiles as $sFile) {
                        AM_Tools_Standard::getInstance()->unlink($sFile);
                    }
                }
                continue;
            }

            AM_Tools_Standard::getInstance()->clearDir($sPath);
        }
    }
}
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
 * @ingroup AM_Handler
 */
class AM_Handler_Thumbnail extends AM_Handler_Abstract implements AM_Handler_Thumbnail_Interface
{
    const IMAGE_TYPE_PNG  = 'png';
    const IMAGE_TYPE_JPEG = 'jpg';
    const IMAGE_TYPE_GIF  = 'gif';

    /** @var array */
    protected $_aSources = array(); /**< @type array */
    /** @var array */
    protected $_aPresets = array(); /**< @type array */
    /** @var AM_Resource_Processor **/
    protected $_oResourceProcessor = null; /**< @type AM_Resource_Processor */
    /** @var AM_Handler_Thumbnail_Storage_Abstract **/
    protected $_oResourceStorage = null; /**< @type AM_Handler_Thumbnail_Storage_Abstract */
    /** @var string **/
    protected $_sImageType = self::IMAGE_TYPE_PNG; /**< @type string */
    /** @var boolean */
    protected $_bZooming = false; /**< @type boolean */

    /**
     * Returns is zooming enabled
     * @return boolean
     */
    public function getZooming()
    {
        return $this->_bZooming;
    }

    /**
     * Sets zooming enabling/disabling
     * @param boolean $bZooming
     * @return AM_Handler_Thumbnail
     */
    public function setZooming($bZooming)
    {
        $this->_bZooming = (boolean) $bZooming;

        return $this;
    }

    /**
     * Returns type of image for conversion
     * @return string
     */
    public function getImageType()
    {
        return $this->_sImageType;
    }

    /**
     * Set type of image for conversion
     * @param string $sImageType
     * @return AM_Handler_Thumbnail
     */
    public function setImageType($sImageType)
    {
        $this->_sImageType = $sImageType;

        return $this;
    }

    /**
     * Set ID of related element
     * @param int $iElementId
     * @return AM_Handler_Thumbnail
     */
    public function setElementId($iElementId)
    {
        $this->_iElementId = $iElementId;

        return $this;
    }

    /**
     * @param array | null $aFiles array of image files
     * @param array | null $aPresets  array of presets
     */
    public function __construct($iElementId = null, $aFiles = null, $aPresets = null)
    {
        if (!is_null($iElementId)) {
            $this->_iElementId = $iElementId;
        }

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
     * Returns the storage
     * @return AM_Handler_Thumbnail_Storage_Abstract
     * @throws AM_Handler_Thumbnail_Exception
     */
    public function getResourceStorage()
    {
        if (is_null($this->_oResourceStorage)) {
            $this->_oResourceStorage = Zend_Registry::get('resourcestorage');
            if (!is_object($this->_oResourceStorage)) {
                throw new AM_Handler_Thumbnail_Exception(sprintf('Wrong resource storage given', 501));
            }
        }

        return $this->_oResourceStorage;
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
     * Returns path to the resized resource
     *
     * @param string $sPreset The name of resizing preset
     * @param string $sResourceType The type of resource (element, toc, static-pdf, etc.)
     * @param int $iId
     * @param string $sFileName
     * @return string
     */
    public function getResourceUrl($sPreset, $sResourceType, $iId = null, $sFileName = null, $sFileExtension = null)
    {
        if (is_null($iId) || is_null($sFileName)) {
            $sImageUrl = '/' . ltrim($this->getPresetDefaultThumbnailUrl($sPreset), '/');

            return $sImageUrl;
        }

        $aPathInfo = pathinfo($sFileName);
        if (!empty($aPathInfo['extension'])
            && ('pdf' == Zend_Filter::filterStatic($aPathInfo['extension'], 'StringToLower', array('encoding' => 'UTF-8')))) {

            $sFileName = $aPathInfo['filename'] . '.png';
        }
        elseif ($sFileExtension) {
          $sFileName = $aPathInfo['filename'] . '.' . $sFileExtension;
        }

        $sImageUrl = $this->getResourceStorage()->getResourceUrl($sPreset, $sResourceType, $iId, $sFileName);

        return $sImageUrl;
    }

    /**
     * Create thumbnails for each source
     * @return AM_Handler_Thumbnail
     */
    public function createThumbnails($useDummySource = false)
    {
        foreach ($this->_aSources as $oSource) {
            /* @var $oSource AM_Resource_Abstract */
            $sInputFile = !$useDummySource
                    ? $oSource->getFileForThumbnail($this->getImageType())
                    : APPLICATION_PATH . "/../front/img/thumbnails/default.png";
            foreach ($this->_aPresets as $sPreset) {
                if (!isset($this->getConfig()->{$sPreset})) {
                    continue;
                }

                $oPresetConfig = $this->getConfig()->{$sPreset};
                //Get path without 'sourceFolder'
                $sPathPrefix = substr($oSource->getSourceFileDir(), strlen($this->getConfig()->common->sourceFolder));
                $sPathPrefix = $sPreset . DIRECTORY_SEPARATOR . trim($sPathPrefix, DIRECTORY_SEPARATOR);

                $this->getResourceStorage()->setPathPrefix($sPathPrefix);

                $sTempPath = AM_Handler_Temp::getInstance()->getDir();

                if ($oSource->isImage()) {
                    $sThumbnail = $sTempPath
                    . DIRECTORY_SEPARATOR
                    . $oSource->getSourceFileName()
                    . '.'
                    . $this->getImageType();

                    $sThumbnailZoom = null;

                    if ('none' != $sPreset && $this->getZooming()) {
                        $sThumbnailZoom = $sTempPath
                                . DIRECTORY_SEPARATOR
                                . $oSource->getSourceFileName()
                                . '_2x'
                                . '.'
                                . $this->getImageType();
                    }

                    $this->getResourceProcessor()->resizeImage($sInputFile, $sThumbnail, $oPresetConfig->width, $oPresetConfig->height, $oPresetConfig->method, $sThumbnailZoom);

                    $this->getResourceStorage()->addResource($sThumbnail);

                    if ('none' != $sPreset) {
                        $iBlockSize = AM_Tools_Image::TILE_SIZE;
                        if ($oPresetConfig->width == 1536 || $oPresetConfig->width == 2048) {
                            $iBlockSize = AM_Tools_Image::TILE_SIZE_RETINA;
                        }

                        $sArchivePath = $sTempPath
                        . DIRECTORY_SEPARATOR
                        . $oSource->getSourceFileName()
                        . '.zip';

                        $this->getResourceProcessor()->cropImage($sInputFile, $sThumbnail, $sArchivePath, $iBlockSize, $sThumbnailZoom);

                        $this->getResourceStorage()->addResource($sArchivePath);
                    }
                } else {
                    //We have to copy not image resource only to the 'none' preset folder
                    if ('none' == $sPreset) {
                        $this->getResourceStorage()->addResource($oSource->getFileForThumbnail($this->getImageType()));
                    }
                }
                $this->getResourceStorage()->save();
            }
        }

        if (!is_null($this->_iElementId)) {
            $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', $this->_iElementId);
            /** @var AM_Model_Db_Element $oElement */
            $oElement->updated = new Zend_Db_Expr('NOW()');
            $oElement->save();
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
     * Load all presets for given resource type
     *
     * @param string $sResourceType
     * @param boolean $bUrgent Should load urgent presets or not urgent
     * @return AM_Handler_Thumbnail
     */
    public function loadAllPresets($sResourceType, $bUrgent = null)
    {
        $this->clearPresets();
        $oResourcesConfig = $this->getConfig()->common->resource;
        $aPresets         = array();

        if (!isset($oResourcesConfig->{$sResourceType})) {
            throw new AM_Handler_Thumbnail_Exception(sprintf('Configuration for preset "%s" not found', $sResourceType));
        }

        $aResourcePresets = $oResourcesConfig->{$sResourceType}->toArray();
        foreach ($aResourcePresets as $sResourcePreset) {
            if (!isset($this->getConfig()->{$sResourcePreset})) {
                throw new AM_Handler_Thumbnail_Exception(sprintf('Can\'t find preset "%s"', $sResourcePreset));
            }

            $aPresetProperties = $this->getConfig()->{$sResourcePreset}->toArray();
            if (is_null($bUrgent)) {
                $this->_aPresets[] = $sResourcePreset;
            } elseif (true === $bUrgent) {
                if (array_key_exists('urgent', $aPresetProperties) && 1 == $aPresetProperties['urgent']) {
                    $this->_aPresets[] = $sResourcePreset;
                }
            } else {
                if (array_key_exists('urgent', $aPresetProperties) && 0 == $aPresetProperties['urgent']) {
                    $this->_aPresets[] = $sResourcePreset;
                } elseif (!array_key_exists('urgent', $aPresetProperties)) {
                    $this->_aPresets[] = $sResourcePreset;
                }
            }
        }

        if (is_null($bUrgent) || true === $bUrgent) {
            $this->_aPresets = array_merge($this->_aPresets, $oResourcesConfig->default->toArray());
        }

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

    /**
     * @param type $sResourceType
     * @param type $iId
     * @param type $sFileName
     */
    public function clearThumbnails($sResourceType, $sPresetGroup = null, $iId = null, $sFileName = null)
    {
        if (is_null($sPresetGroup)) {
            $sPresetGroup = $sResourceType;
        }

        $this->loadAllPresets($sPresetGroup);

        $sSplittedIdPath = is_null($iId) ? null : trim(AM_Tools_String::generatePathFromId($iId), DIRECTORY_SEPARATOR);

        foreach ($this->_aPresets as &$sValue) {
            $sPath = $sValue;

            if (!empty($sResourceType)) {
                $sPath .= DIRECTORY_SEPARATOR . $sResourceType;
            }

            if (!is_null($sSplittedIdPath)) {
                $sPath .= DIRECTORY_SEPARATOR . $sSplittedIdPath;
            }

            $this->getResourceStorage()->setPathPrefix($sPath)->clearResources($sFileName);
        }
    }
}
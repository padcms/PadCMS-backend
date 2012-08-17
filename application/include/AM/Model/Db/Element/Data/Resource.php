<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Resource class definition.
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
 * Behavior of element that has one or more files (resources) as data
 * @todo Rename
 * @ingroup AM_Model
 */
abstract class AM_Model_Db_Element_Data_Resource extends AM_Model_Db_Element_Data_Abstract
{
    const DATA_KEY_RESOURCE   = 'resource';
    const DATA_KEY_IMAGE_TYPE = 'image_type';
    const PDF_INFO            = 'pdf_info';

    /** @var string The name of resource */
    protected $_sResourceName            = null; /**< @type string */
    /** @var string The path of resource */
    protected $_sResourceDir             = null; /**< @type string */
    /** @var array The names of additional files */
    protected $_aAdditionalResourcesKeys = array(); /**< @type array */

    /** @var AM_Handler_Upload **/
    protected $_oUploadHandler       = null;

    protected function _init()
    {
        $this->_sResourceName = $this->_getResourceName();
        $this->_sResourceDir  = $this->_getResourceDir();
    }

    /**
     * Get resource name
     * @return string
     */
    protected function _getResourceName()
    {
        $sResourceName = $this->getDataValue(self::DATA_KEY_RESOURCE);

        $sResourceName = self::DATA_KEY_RESOURCE . '.' . pathinfo($sResourceName, PATHINFO_EXTENSION);

        return $sResourceName;
    }

    /**
     * Get resource path
     * @return string
     */
    protected function _getResourceDir()
    {
        $sResourceDir = AM_Tools::getContentPath(self::TYPE, $this->getElement()->id);

        return $sResourceDir;
    }

    /**
     * Add additionsl resource key
     * @param string | array $key
     * @return AM_Model_Db_Element_Data_Resource
     */
    public function addAdditionalResourceKey($mKey)
    {
        if (empty($mKey)) {
            throw new AM_Model_Db_Element_Data_Exception('Trying to set empty additional resource key');
        }

        if (is_array($mKey)) {
            $this->_aAdditionalResourcesKeys = array_merge($this->_aAdditionalResourcesKeys, $mKey);
        } else {
            $this->_aAdditionalResourcesKeys[] = $mKey;
        }

        return $this;
    }

    /**
     * Get file uploader
     * @return AM_Handler_Upload
     */
    public function getUploader()
    {
        if (is_null($this->_oUploadHandler)) {
            $this->_oUploadHandler = new AM_Handler_Upload();
        }

        return $this->_oUploadHandler;
    }

    /**
     * Set uploader
     * @param AM_Handler_Upload $oUploader
     * @return AM_Model_Db_Element_Data_Resource
     */
    public function setUploader(AM_Handler_Upload $oUploader)
    {
        $this->_oUploadHandler = $oUploader;

        return $this;
    }

    /**
     * @see AM_Model_Db_Element_Data_Interface::copy()
     */
    protected function _postCopy()
    {
        if (empty($this->_sResourceName)) {
            return $this;
        }

        $sOldResourcePath = $this->_sResourceDir . DIRECTORY_SEPARATOR . $this->_sResourceName;
        $sNewResourceDir  = $this->_getResourceDir();
        $sNewResourcePath = $sNewResourceDir . DIRECTORY_SEPARATOR . $this->_sResourceName;

        if ($sOldResourcePath === $sNewResourcePath) {
            return $this;
        }

        if (AM_Tools_Standard::getInstance()->is_dir($this->_sResourceDir)) {
            if (!AM_Tools_Standard::getInstance()->is_dir($sNewResourceDir)) {
                if (!AM_Tools_Standard::getInstance()->mkdir($sNewResourceDir, 0777, true)) {
                    throw new AM_Model_Db_Element_Data_Exception("I/O error while create dir '{$sNewResourceDir}'");
                }
            }
            $aResourcesForThumbnails = array();
            if (!AM_Tools_Standard::getInstance()->copy($sOldResourcePath, $sNewResourcePath)) {
                throw new AM_Model_Db_Element_Data_Exception("I/O error while copy files from '{$sOldResourcePath}' to '{$sNewResourcePath}'");
            }
            $aResourcesForThumbnails[] = $sNewResourcePath;
            foreach ($this->_aAdditionalResourcesKeys as $sResourceKey) {
                $sFile = $this->getDataValue($sResourceKey);
                if ($sFile) {
                    $sFileExtension = strtolower(pathinfo($sFile, PATHINFO_EXTENSION));

                    $sOldResourcePath = $this->_sResourceDir . DIRECTORY_SEPARATOR . $sResourceKey . "." . $sFileExtension;
                    $sNewResourcePath = $this->_getResourceDir() . DIRECTORY_SEPARATOR . $sResourceKey . "." . $sFileExtension;

                    if (!AM_Tools_Standard::getInstance()->copy($sOldResourcePath, $sNewResourcePath)) {
                        throw new AM_Model_Db_Element_Data_Exception("I/O error while copy files from '{$sOldResourcePath}' to '{$sNewResourcePath}'");
                    }

                    $aResourcesForThumbnails[] = $sNewResourcePath;
                }
            }

            foreach ($aResourcesForThumbnails as $destination) {
                $this->_postUpload($destination);
            }
        }
    }

    /**
     * Upload resources
     * @return AM_Model_Db_Element_Data_Resource
     */
    protected function _preSave()
    {
        //Path for saving files
        $sDataPath = $this->_getResourceDir();
        if (!AM_Tools_Standard::getInstance()->is_dir($sDataPath)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sDataPath, 0777, true)) {
                throw new AM_Model_Db_Element_Data_Exception("I/O error while create dir '{$sDataPath}'");
            }
        }

        $aKeys = array_merge($this->_aAdditionalResourcesKeys, array(self::DATA_KEY_RESOURCE));

        foreach ($aKeys as $sKey) {
            $oElementDataRow      = $this->getDataRow($sKey);
            //Checking that we have not saved row
            //we have created it by:
            //- AM_Model_Db_Element_Data_Abstract::setData method)
            //- AM_Model_Db_Element_Data_Abstract::addKeyValue ($key, $value, FALSE)
            if ($oElementDataRow->isNew()) {
                $sResource = $this->getDataValue($sKey);

                if ($sResource) {
                    $sResource      = $this->_normalizePath($sResource);
                    $aFileInfo      = pathinfo($sResource);
                    $sFileName      = $aFileInfo["filename"];
                    $sFileExtension = $aFileInfo["extension"];
                    $sFileNameNew   = $sKey . "." . $sFileExtension;

                    $this->addKeyValue($sKey, $sFileNameNew);

                    //Copy resource
                    $sResourceNew = $sDataPath . DIRECTORY_SEPARATOR . $sFileNameNew;
                    if (!AM_Tools_Standard::getInstance()->copy($sResource, $sResourceNew)) {
                        throw new AM_Model_Db_Element_Data_Exception(sprintf('I/O error while copy files from "%s" to "%s"', $sResource, $sResourceNew));
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Post save trigger
     *
     * @return AM_Model_Db_Element_Data_Resource
     */
    protected function _postSave()
    {
        $aKeys = array_merge($this->_aAdditionalResourcesKeys, array(self::DATA_KEY_RESOURCE));

        foreach ($aKeys as $sKey) {
            $sResource = $this->getDataValue($sKey);

            if ($sResource) {
                $sDataPath = $this->_getResourceDir();
                $sFileName = $sKey . "." . pathinfo($sResource, PATHINFO_EXTENSION);
                $sResource = $sDataPath . DIRECTORY_SEPARATOR . $sFileName;

                $this->_postUpload($sResource, $sKey);
            }
        }

        return $this;
    }

    /**
     * Check path - reletive or absolute, parse correct path
     * @param string $sPath
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _normalizePath($sPath)
    {
        if (!AM_Tools_Standard::getInstance()->file_exists($sPath)) {
            $sPath = $this->getTempPath() . DIRECTORY_SEPARATOR . ltrim($sPath, DIRECTORY_SEPARATOR);
            if (!AM_Tools_Standard::getInstance()->file_exists($sPath)) {
                throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong resource given "%s"', $sPath));
            }
        }

        return $sPath;
    }

    /**
     * @return AM_Model_Db_Element_Data_Resource
     */
    public function setPageBackground()
    {
        $sResource      = $this->getDataValue(self::DATA_KEY_RESOURCE);
        $sFileExtension = pathinfo($sResource, PATHINFO_EXTENSION);

        if ($sResource) {
            $oPageBackground           = new AM_Model_Db_PageBackground();
            $oPageBackground->id       = $this->getElement()->id;
            $oPageBackground->page     = $this->getElement()->page;
            $oPageBackground->type     = self::TYPE;
            $oPageBackground->filename = self::DATA_KEY_RESOURCE . "." . $sFileExtension;
            $oPageBackground->save();
        }

        return $this;
    }

    /**
     * Get allowed file extensions
     * @throws AM_Model_Db_Element_Data_Exception
     * @return array
     */
    public static function getAllowedFileExtensions($sResourceKey = self::DATA_KEY_RESOURCE)
    {
        $aAllowedExtensionsByResource = static::$_aAllowedFileExtensions;
        if (!array_key_exists($sResourceKey, $aAllowedExtensionsByResource)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('File with variable name "%s" not found', $sResourceKey));
        }

        return $aAllowedExtensionsByResource[$sResourceKey];
    }

    /**
     * Upload resource
     * @param string $sResourceKey resource name
     * @return AM_Model_Db_Element_Data_Resource
     * @throws AM_Model_Db_Element_Data_Exception
     */
    public function upload($sResourceKey = self::DATA_KEY_RESOURCE)
    {
        $oUploader = $this->getUploader();
        if (!$oUploader->isUploaded($sResourceKey)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('File with variable name "%s" not found', $sResourceKey));
        }

        //Validate uploaded file
        $oUploader->addValidator('Size', true, array('min' => 20, 'max' => 209715200))
                ->addValidator('Count', true, 1)
                ->addValidator('Extension', true, self::getAllowedFileExtensions($sResourceKey));

        if (!$oUploader->isValid()) {
            throw new AM_Model_Db_Element_Data_Exception($oUploader->getMessagesAsString());
        }

        $sDataPath = $this->_getResourceDir();
        if (!AM_Tools_Standard::getInstance()->is_dir($sDataPath)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sDataPath, 0777, true)) {
                throw new AM_Model_Db_Element_Data_Exception(sprintf('I/O error while create dir "%s"', $sDataPath));
            }
        }

        $aFiles         = $oUploader->getFileInfo();
        $sGivenFileName = $aFiles[$sResourceKey]['name'];
        $aFileInfo      = pathinfo($sGivenFileName);
        $sFileExtension = $aFileInfo['extension'];
        $sFileNameNew    = $sResourceKey . "." . $sFileExtension;

        //Copy resource
        $sDestination = $sDataPath . DIRECTORY_SEPARATOR . $sFileNameNew;

        //Rename file and receive it
        $oUploader->addFilter('Rename', array('target' => $sDestination, 'overwrite' => true));

        $this->clearResources($sResourceKey);

        if (!$oUploader->receive($sResourceKey)) {
            throw new AM_Model_Db_Element_Data_Exception($oUploader->getMessagesAsString());
        }

        $this->addKeyValue($sResourceKey, $sGivenFileName);
        $this->addKeyValue(self::DATA_KEY_IMAGE_TYPE, $this->getImageType());

        $this->_postUpload($sDestination, $sResourceKey);


        return $this;
    }

    /**
     * Post upload trigger
     * @param string $sDestination The path to file that has been uploaded
     * @param string $sKey
     * @return AM_Model_Db_Element_Data_Resource
     */
    protected function _postUpload($sDestination, $sKey = null)
    {
        $oThumbnailer = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
        /* @var $oThumbnailer AM_Handler_Thumbnail */
        $oThumbnailer->clearSources()
                ->addSourceFile($sDestination)
                ->setImageType($this->getImageType())
                ->loadAllPresets($this->getThumbnailPresetName())
                ->createThumbnails();

        foreach ($oThumbnailer->getSources() as $oSource) {
            if ($oSource->isPdf() && self::DATA_KEY_RESOURCE == $sKey) {
                //Remove old pdf_info
                $this->delete(self::PDF_INFO, false);
                $sPdfInfo = $oSource->getPdfInfo();
                $this->addKeyValue(self::PDF_INFO, $sPdfInfo);

                return $this;
            }
        }

        return $this;
    }

    /**
     * Returns type of image for conversion
     * @return string
     */
    public function getImageType()
    {
        return AM_Handler_Thumbnail::IMAGE_TYPE_PNG;
    }

    /**
     * Clear resources and thumbnail
     *
     * @param string|null $sKey Have to delete data by $key or all data
     * @return void
     */
    public function clearResources($sKey = null)
    {
        if (is_null($sKey)) {
            $sKey = '*';
        }
        AM_Tools::clearContent(self::TYPE, $this->getElement()->id, $sKey . '.*');
        AM_Tools::clearResizerCache(self::TYPE, $this->getElement()->getResources()->getThumbnailPresetName(), $this->getElement()->id, $sKey . '.*');
    }

    /**
     * Clear resources after data deleting
     *
     * @param string|null $sKey Have to delete data by $key or all data
     * @return void
     */
    protected function _postDelete($sKey = null)
    {
        //Remove pdf information
        if (self::DATA_KEY_RESOURCE == $sKey) {
            $this->delete(self::PDF_INFO, false);
        }

        //Remove resources ans thumbnails
        $this->clearResources($sKey);
    }

    /**
     * Returns path to resource for manifest
     *
     * @return string|false
     */
    protected function _getExportResource()
    {
        $sValue = $this->_getResourcePathForExport(self::DATA_KEY_RESOURCE);

        return $sValue;
    }

    /**
     * Returns path to resource for manifest
     * @param string $sKey
     * @return boolean|string
     */
    protected function _getResourcePathForExport($sKey)
    {
        $sValue = $this->getDataValue($sKey);
        if (empty($sValue)) {
            return false;
        }

        $sFileExtension = pathinfo($sValue, PATHINFO_EXTENSION);
        $sFileExtension = $this->getImageType();

        if (empty($sFileExtension)) {
            return false;
        }

        $sValue =   '/' . AM_Model_Db_Element_Data_Abstract::TYPE
                 . '/' . AM_Tools_String::generatePathFromId($this->getElement()->id)
                 . '/' . $sKey . '.' . $sFileExtension;

        return $sValue;
    }
}
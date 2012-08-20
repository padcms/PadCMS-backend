<?php
/**
 * @file
 * AM_Model_Db_Term_Data_Resource class definition.
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
 * This class encapsulates logic of work with term's resources - files
 * @todo Rename
 * @ingroup AM_Model
 */
class AM_Model_Db_Term_Data_Resource extends AM_Model_Db_Term_Data_Abstract
{
    const RESOURCE_KEY_SUMMARY = 'summary';
    const RESOURCE_KEY_STRIPE  = 'stripe';
    /** @var string The dir where resources are **/
    protected $_sResourceDir = null; /**< @type string */
    /** @var AM_Handler_Upload **/
    protected $_oUploadHandler = null; /**< @type AM_Handler_Upload */

    protected static $_aAllowedFileExtensions = array(
        self::RESOURCE_KEY_SUMMARY => array('pdf', 'jpg', 'jpeg', 'png'),
        self::RESOURCE_KEY_STRIPE  => array('pdf', 'jpg', 'jpeg', 'png')
    );

    protected function init()
    {
        $this->_sResourceDir  = $this->_getResourceDir();
    }

    /**
     * Get resource path
     * @return string
     */
    protected function _getResourceDir()
    {
        $sResourceDir = AM_Tools::getContentPath(self::TYPE, $this->getTerm()->id);

        return $sResourceDir;
    }

    /**
     * Copy file resource
     * @param string $sFileBaseName
     * @param string $sResourceKey resource key (summary, stripe)
     * @return AM_Model_Db_Term_Data_Resource
     */
    protected function _copyFile($sFileBaseName, $sResourceKey)
    {
        $sExtension = strtolower(pathinfo($sFileBaseName, PATHINFO_EXTENSION));

        $sOldResourcePath = $this->_sResourceDir . DIRECTORY_SEPARATOR . $sResourceKey . "." . $sExtension;
        $sNewResourceDir  = $this->_getResourceDir();
        $sNewResourcePath = $sNewResourceDir . DIRECTORY_SEPARATOR . $sResourceKey . "." . $sExtension;

        if ($sOldResourcePath === $sNewResourcePath) {
            return $this;
        }

        if (!AM_Tools_Standard::getInstance()->is_dir($sNewResourceDir)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sNewResourceDir, 0777, true)) {
                throw new AM_Model_Db_Term_Data_Exception("I/O error while create dir '{$sNewResourceDir}'");
            }
        }

        if (!AM_Tools_Standard::getInstance()->copy($sOldResourcePath, $sNewResourcePath)) {
            throw new AM_Model_Db_Term_Data_Exception("I/O error while copy files from '{$sOldResourcePath}' to '{$sNewResourcePath}'");
        }

        $this->_postUpload($sNewResourcePath);

        return $this;
    }

    /**
     * @see AM_Model_Db_Term_Data_Interface::copy()
     */
    public function copy()
    {
        if (empty($this->_sThumbSummary) && empty($this->_sThumbStripe)) {
            return $this;
        }

        if (!AM_Tools_Standard::getInstance()->is_dir($this->_sResourceDir)) {
            return $this;
        }

        //Copy thumb_summary
        if (!empty($this->_sThumbSummary)) {
            $this->_copyFile($this->_sThumbSummary, self::RESOURCE_KEY_SUMMARY);
        }

        //Copy thumb_stripe
        if (!empty($this->_sThumbStripe)) {
            $this->_copyFile($this->_sThumbStripe, self::RESOURCE_KEY_STRIPE);
        }
    }

    /**
     * @see AM_Model_Db_Term_Data_Abstract::_preSave()
     */
    protected function _preSave()
    {
        $sDataPath = $this->_getResourceDir();
        //Path for saving files
        if (!AM_Tools_Standard::getInstance()->is_dir($sDataPath)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sDataPath, 0777, true)) {
                throw new AM_Model_Db_Term_Data_Exception("I/O error while create dir '{$sDataPath}'");
            }
        }

        if (!empty($this->_sThumbSummary)) {
            $this->_saveFile($this->_sThumbSummary, self::RESOURCE_KEY_SUMMARY);
        }
        if (!empty($this->_sThumbStripe)) {
            $this->_saveFile($this->_sThumbStripe, self::RESOURCE_KEY_STRIPE);
        }
    }

    /**
     * Copy resources files from temp path to resource data dir
     * @param type $sFileName
     * @param type $sResourceKey
     */
    protected function _saveFile($sFileName, $sResourceKey)
    {
        $sDataPath = $this->_getResourceDir();

        $sResource = $this->_normalizePath($sFileName);

        $aFileInfo      = pathinfo($sResource);
        $sFileName      = $aFileInfo["filename"];
        $sFileExtension = $aFileInfo["extension"];
        $sFileNameNew   = $sResourceKey . "." . $sFileExtension;

        //Copy resource
        $sResourceNew = $sDataPath . DIRECTORY_SEPARATOR . $sFileNameNew;
        if (!AM_Tools_Standard::getInstance()->copy($sResource, $sResourceNew)) {
            throw new AM_Model_Db_Term_Data_Exception("I/O error while copy files from '{$sResource}' to '{$sResourceNew}'");
        }
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
                throw new AM_Model_Db_Term_Data_Exception("Wrong resource given '{$sPath}'");
            }
        }

        return $sPath;
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
     * @param AM_Handler_Upload $uploader
     * @return AM_Model_Db_Term_Data_Resource
     */
    public function setUploader(AM_Handler_Upload $uploader)
    {
        $this->_oUploadHandler = $uploader;

        return $this;
    }

    /**
     * Upload resource
     * @param string $sResourceKey resource name
     * @return AM_Model_Db_Term_Data_Resource
     * @throws AM_Model_Db_Term_Data_Exception
     */
    public function upload($sResourceKey)
    {
        $oUploadHandler = $this->getUploader();
        if (!$oUploadHandler->isUploaded($sResourceKey)) {
            throw new AM_Model_Db_Term_Data_Exception(sprintf('File with variable name "%s" not found', $sResourceKey));
        }

        //Validate uploaded file
        $oUploadHandler->addValidator('Size', true, array('min' => 20, 'max' => 209715200))
                ->addValidator('Count', true, 1)
                ->addValidator('Extension', true, self::getAllowedFileExtensions($sResourceKey));

        if (!$oUploadHandler->isValid()) {
            throw new AM_Model_Db_Term_Data_Exception($oUploadHandler->getMessagesAsString());
        }

        $sDataPath = $this->_getResourceDir();
        if (!AM_Tools_Standard::getInstance()->is_dir($sDataPath)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sDataPath, 0777, true)) {
                throw new AM_Model_Db_Term_Data_Exception(sprintf('I/O error while create dir "%s"', $sDataPath));
            }
        }

        $aFiles         = $oUploadHandler->getFileInfo();
        $sGivenFile     = $aFiles[$sResourceKey]['name'];
        $aFileInfo      = pathinfo($sGivenFile);
        $sFileName      = $aFileInfo["filename"];
        $sFileExtension = $aFileInfo["extension"];
        $sFileNameNew   = $sResourceKey . "." . $sFileExtension;

        //Copy resource
        $sDestination = $sDataPath . DIRECTORY_SEPARATOR . $sFileNameNew;

        //Rename file and receive it
        $oUploadHandler->addFilter('Rename', array('target' => $sDestination, 'overwrite' => true));

        $this->clearResources($sResourceKey);

        if (!$oUploadHandler->receive($sResourceKey)) {
            throw new AM_Model_Db_Term_Data_Exception($oUploadHandler->getMessagesAsString());
        }

        $sField = 'thumb_' . $sResourceKey;
        $this->getTerm()->$sField  = $sFileName . '.' . $sFileExtension;
        $this->getTerm()->updated = new Zend_Db_Expr('NOW()');
        $this->getTerm()->save();

        $this->_postUpload($sDestination);

        return $this;
    }

    /**
     * Post upload trigger
     * @param type $sDestination
     * @return AM_Model_Db_Term_Data_Resource
     */
    protected function _postUpload($sDestination)
    {
        if (AM_Tools::isAllowedImageExtension($sDestination)) {
            $oThumbnailerHandler = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
            /* @var $oThumbnailerHandler AM_Handler_Thumbnail */
            $oThumbnailerHandler->clearSources()
                    ->setImageType($this->getImageType())
                    ->addSourceFile($sDestination)
                    ->loadAllPresets(self::TYPE)
                    ->createThumbnails();
        }

        return $this;
    }

    /**
     * Returns type of image for conversion
     * @return string
     */
    public function getImageType()
    {
        return AM_Handler_Thumbnail::IMAGE_TYPE_JPEG;
    }

    /**
     * Get allowed file extensions
     * @param string $sResourceKey
     * @throws AM_Model_Db_Term_Data_Exception
     * @return array
     */
    public static function getAllowedFileExtensions($sResourceKey)
    {
        $aAllowedExtensionsByResource = static::$_aAllowedFileExtensions;
        if (!array_key_exists($sResourceKey, $aAllowedExtensionsByResource)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('File with variable name "%s" not found', $sResourceKey));
        }

        return $aAllowedExtensionsByResource[$sResourceKey];
    }

    /**
     * Clear resources and thumbnail
     *
     * @param string|null $sResourceKey Have to delete data by $key or all data
     * @return void
     */
    public function clearResources($sResourceKey = null)
    {
        if (is_null($sResourceKey)) {
            $sResourceKey = '*';
        }
        AM_Tools::clearContent(AM_Model_Db_Term_Data_Abstract::TYPE, $this->getTerm()->id, $sResourceKey . '.*');
        AM_Tools::clearResizerCache(AM_Model_Db_Term_Data_Abstract::TYPE, AM_Model_Db_Term_Data_Abstract::TYPE, $this->getTerm()->id, $sResourceKey . '.*');
    }

    /**
     * Get resource path for export data
     *
     * @param string $sResourceKey
     * @return string Resorce path
     */
    public function getValueForExport($sResourceKey)
    {
        $sGetterMethod = '_getExport' . ucfirst(Zend_Filter::filterStatic($sResourceKey, 'Word_UnderscoreToCamelCase'));
        if (method_exists($this, $sGetterMethod)) {
            $sValue = $this->$sGetterMethod();
        } else {
            $sProperty = 'thumb_' . $sResourceKey;
            $sValue    = $this->getTerm()->$sProperty;
        }

        return $sValue;
    }

    protected function _getExportSummary()
    {
        $sFileName = $this->getTerm()->thumb_summary;
        if (!empty($sFileName)) {
            $sExtension = pathinfo($sFileName, PATHINFO_EXTENSION);
            $sExtension = Zend_Filter::filterStatic($sExtension, 'StringToLower', array('encoding' => 'UTF-8'));
            $sExtension = ('pdf' == $sExtension) ? 'png' : $sExtension;

            $sPath =   '/' . AM_Model_Db_Term_Data_Abstract::TYPE
                    . '/' . AM_Tools_String::generatePathFromId($this->getTerm()->id)
                    . '/' . self::RESOURCE_KEY_SUMMARY . '.' . $sExtension;

            return $sPath;
        }

        return null;
    }

    protected function _getExportStripe()
    {
        $sFileName = $this->getTerm()->thumb_stripe;
        if (!empty($sFileName)) {
            $sExtension = pathinfo($sFileName, PATHINFO_EXTENSION);
            $sExtension = Zend_Filter::filterStatic($sExtension, 'StringToLower', array('encoding' => 'UTF-8'));
            $sExtension = ('pdf' == $sExtension) ? 'png' : $sExtension;

            $sPath =   '/' . AM_Model_Db_Term_Data_Abstract::TYPE
                    . '/' . AM_Tools_String::generatePathFromId($this->getTerm()->id)
                    . '/' . self::RESOURCE_KEY_STRIPE . '.' . $sExtension;

            return $sPath;
        }

        return null;
    }
}
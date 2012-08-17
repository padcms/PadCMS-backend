<?php
/**
 * @file
 * AM_Model_Db_StaticPdf_Data_Abstract class definition.
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
 * This class encapsulates logic of work with horisontal pdf's resources - files, strings, etc.
 * @todo Rename
 * @ingroup AM_Model
 */
class AM_Model_Db_StaticPdf_Data_Resource extends AM_Model_Db_StaticPdf_Data_Abstract
{
    const INPUT_FILE_NAME = 'pdf-file';
    /** @var string The dir where resources are **/
    protected $_sResourceDir    = null; /**< @type string */
    /** @var string Resource name on disk (without extension) **/
    protected $_sResourceName   = null; /**< @type string */
    /** @var string The base name (with extension) of uploaded file, this name is store in DB **/
    protected $_sResourceDbBaseName = null; /**< @type string */
    /** @var AM_Handler_Upload **/
    protected $_oUploadHandler       = null; /**< @type AM_Handler_Upload */

    protected static $_aAllowedExtensions = array('pdf');

    protected function _init()
    {
        $this->_sResourceDir        = $this->getResourceDir();
        $this->_sResourceName       = $this->_getHorizontalPdf()->id;
        $this->_sResourceDbBaseName = $this->_getHorizontalPdf()->name;
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
     * @param AM_Handler_Upload $oUploadHandler
     * @return AM_Model_Db_StaticPdf_Data_Resource
     */
    public function setUploader(AM_Handler_Upload $oUploadHandler)
    {
        $this->_oUploadHandler = $oUploadHandler;

        return $this;
    }

    /**
     * Get resource path
     * @return string
     */
    public function getResourceDir()
    {
        $sResourceDir = AM_Tools::getContentPath(self::TYPE, $this->_getHorizontalPdf()->issue);

        return $sResourceDir;
    }

    /**
     * Get base name of uploaded file
     * @return string
     */
    public function getResourceDbBaseName()
    {
        return $this->_sResourceDbBaseName;
    }

    /**
     * Get resource base name (with extension)
     * @return string
     */
    public function getResourceBaseName()
    {
        $sExtension = strtolower(pathinfo($this->_sResourceDbBaseName, PATHINFO_EXTENSION));

        return $this->_sResourceName . '.' . $sExtension;
    }

    /**
     * Copy resource files if statis_pdf id was changed
     * @return AM_Model_Db_StaticPdf_Data_Resource
     * @throws AM_Model_Db_StaticPdf_Data_Exception
     */
    public function copy()
    {
        if (empty($this->_sResourceDbBaseName)) {
            return $this;
        }

        if (!AM_Tools_Standard::getInstance()->is_dir($this->_sResourceDir)) {
            return $this;
        }

        $sExtension = strtolower(pathinfo($this->_sResourceDbBaseName, PATHINFO_EXTENSION));

        $sOldResourcePath = $this->_sResourceDir . DIRECTORY_SEPARATOR . $this->_sResourceName . "." . $sExtension;
        $sNewResourceDir  = $this->getResourceDir();
        $sNewResourcePath = $sNewResourceDir . DIRECTORY_SEPARATOR . $this->_getHorizontalPdf()->id . "." . $sExtension;

        if ($sOldResourcePath === $sNewResourcePath) {
            return $this;
        }

        if (!AM_Tools_Standard::getInstance()->is_dir($sNewResourceDir)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($sNewResourceDir, 0777, true)) {
                throw new AM_Model_Db_StaticPdf_Data_Exception("I/O error while create dir '{$sNewResourceDir}'");
            }
        }

        if (!AM_Tools_Standard::getInstance()->copy($sOldResourcePath, $sNewResourcePath)) {
            throw new AM_Model_Db_StaticPdf_Data_Exception("I/O error while copy files from '{$sOldResourcePath}' to '{$sNewResourcePath}'");
        }

        $this->_postUpload($sNewResourcePath);

        return $this;
    }

    /**
     * Upload resource file
     * @return AM_Model_Db_StaticPdf_Data_Resource
     * @throws AM_Model_Db_StaticPdf_Data_Exception
     */
    public function upload() {
        if (!AM_Tools_Standard::getInstance()->is_dir($this->_sResourceDir)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($this->_sResourceDir, 0777, true)) {
                throw new AM_Model_Db_StaticPdf_Data_Exception(sprintf('I/O error while create dir "%s"', $this->_sResourceDir));
            }
        }

        $oUploadHandler = $this->getUploader();
        if (!$oUploadHandler->isUploaded(self::INPUT_FILE_NAME)) {
            throw new AM_Model_Db_StaticPdf_Data_Exception('File with variable name "pdf-file" not found');
        }

        //Validate uploaded file
        $oUploadHandler->addValidator('Size', true, array('min' => 20, 'max' => 209715200))
                ->addValidator('Count', true, 1)
                ->addValidator('Extension', true, self::$_aAllowedExtensions);

        if (!$oUploadHandler->isValid()) {
            throw new AM_Model_Db_StaticPdf_Data_Exception($oUploadHandler->getMessagesAsString());
        }

        $aFiles         = $oUploadHandler->getFileInfo();
        $sFileExtension = strtolower(pathinfo($aFiles[self::INPUT_FILE_NAME]['name'], PATHINFO_EXTENSION));
        $sDestination   = $this->_sResourceDir . DIRECTORY_SEPARATOR . $this->_sResourceName . '.' . $sFileExtension;
        //Rename file and receive it
        $oUploadHandler->addFilter('Rename', array('target' => $sDestination, 'overwrite' => true));

        if (!$oUploadHandler->receive(self::INPUT_FILE_NAME)) {
            throw new AM_Model_Db_StaticPdf_Data_Exception($oUploadHandler->getMessagesAsString());
        }

        $this->_sResourceDbBaseName = $aFiles[self::INPUT_FILE_NAME]['name'];

        $this->_postUpload($sDestination);

        return $this;
    }

    /**
     * Post upload trigger
     * @param string $sDestination
     * @return AM_Model_Db_StaticPdf_Data_Resource
     */
    protected function _postUpload($sDestination)
    {
        if (AM_Tools::isAllowedImageExtension($sDestination)) {
            $oThumbnailerHandler = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
            /* @var $oThumbnailerHandler AM_Handler_Thumbnail */
            $oThumbnailerHandler->clearSources()
                    ->addSourceFile($sDestination)
                    ->loadAllPresets(self::TYPE)
                    ->createThumbnails();
        }
        return $this;
    }

    /**
     * Get first page of PDF file and conver it the PNG
     * @return string
     * @throws AM_Model_Db_StaticPdf_Data_Exception
     */
    public function getFirstPageAsPng()
    {
        $sResource = $this->_sResourceDir  . DIRECTORY_SEPARATOR . $this->getResourceBaseName();

        $oImageSource = AM_Resource_Factory::create($sResource);

        if (! $oImageSource instanceof AM_Resource_Concrete_Pdf) {
            throw new AM_Model_Db_StaticPdf_Data_Exception(sptintf('Wrong resource given "%s". Must be a PDF resource', $sResource));
        }

        $sFirstPage = $oImageSource->getFileForThumbnail();

        return $sFirstPage;
    }

    /**
     * Get all page of PDF and convert them to the png
     * @return array
     * @throws AM_Model_Db_StaticPdf_Data_Exception
     */
    public function getAllPagesThumbnails()
    {
        $sResource = $this->_sResourceDir  . DIRECTORY_SEPARATOR . $this->getResourceBaseName();

        $oImageSource = AM_Resource_Factory::create($sResource);

        if (! $oImageSource instanceof AM_Resource_Concrete_Pdf) {
            throw new AM_Model_Db_StaticPdf_Data_Exception(sptintf('Wrong resource given "%s". Must be a PDF resource', $sResource));
        }

        $aFiles = $oImageSource->getAllPagesThumbnails();

        return $aFiles;
    }

    /**
     * Allows post-delete logic to be applied to resource.
     *
     * @return void
     */
    protected function _postDelete()
    {
        $this->clearResources();
    }

    /**
     * Clear resources and thumbnail
     *
     * @return void
     */
    public function clearResources()
    {
        AM_Tools::clearContent(self::TYPE, $this->_getHorizontalPdf()->issue);
        AM_Tools::clearResizerCache(self::TYPE, self::TYPE, $this->_getHorizontalPdf()->issue);

        AM_Tools::clearContent(self::TYPE_CACHE, $this->_getHorizontalPdf()->issue);
        AM_Tools::clearResizerCache(self::TYPE_CACHE, self::TYPE_CACHE, $this->_getHorizontalPdf()->issue);
    }
}
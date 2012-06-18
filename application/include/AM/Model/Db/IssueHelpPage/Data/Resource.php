<?php
/**
 * @file
 * AM_Model_Db_IssueHelpPage_Data_Resource class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @todo Rename
 * @ingroup AM_Model
 */
class AM_Model_Db_IssueHelpPage_Data_Resource extends AM_Model_Db_IssueHelpPage_Data_Abstract
{
    const INPUT_FILE_NAME = 'help-page-file';

    /** @var string The dir where resources are */
    protected $_sResourceDir    = null; /**< @type string */
    /** @var string Resource name on disk (without extension) */
    protected $_sResourceName   = null; /**< @type string */
    /** @var string The base name (with extension) of uploaded file, this name is store in DB */
    protected $_sResourceDbBaseName = null; /**< @type string */
    /** @var AM_Handler_Upload */
    protected $_oUploadHandler       = null; /**< @type AM_Handler_Upload */

    protected static $_aAllowedExtensions = array('pdf');

    protected function _init()
    {
        $this->_sResourceDir        = $this->getResourceDir();
        $this->_sResourceName       = $this->_getIssueHelpPage()->type;
        $this->_sResourceDbBaseName = $this->_getIssueHelpPage()->name;
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
     * @return AM_Model_Db_IssueHelpPage_Data_Resource
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
        $sResourceDir = AM_Tools::getContentPath($this->_getIssueHelpPage()->getThumbnailPresetType(), $this->_getIssueHelpPage()->id_issue);

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
        $sExtension = strtolower(pathinfo($this->getResourceDbBaseName(), PATHINFO_EXTENSION));

        return $this->_getIssueHelpPage()->type . '.' . $sExtension;
    }

    /**
     * Upload resource file
     * @return AM_Model_Db_IssueHelpPage_Data_Resource
     * @throws AM_Model_Db_IssueHelpPage_Data_Exception
     */
    public function upload()
    {
        AM_Tools::clearContent($this->_getIssueHelpPage()->getThumbnailPresetType(), $this->_getIssueHelpPage()->id_issue);
        AM_Tools::clearResizerCache($this->_getIssueHelpPage()->getThumbnailPresetType(), $this->_getIssueHelpPage()->id_issue);

        if (!AM_Tools_Standard::getInstance()->is_dir($this->_sResourceDir)) {
            if (!AM_Tools_Standard::getInstance()->mkdir($this->_sResourceDir, 0777, true)) {
                throw new AM_Model_Db_IssueHelpPage_Data_Exception(sprintf('I/O error while create dir "%s"', $this->_sResourceDir));
            }
        }

        $oUploadHandler = $this->getUploader();
        $sInputName = $this->_getIssueHelpPage()->type . '-' . self::INPUT_FILE_NAME;
        if (!$oUploadHandler->isUploaded($sInputName)) {
            throw new AM_Model_Db_IssueHelpPage_Data_Exception(sprintf('File with variable name "%s" not found', $sInputName));
        }

        //Validate uploaded file
        $oUploadHandler->addValidator('Size', true, array('min' => 20, 'max' => 209715200))
                ->addValidator('Count', true, 1)
                ->addValidator('Extension', true, self::$_aAllowedExtensions);

        if (!$oUploadHandler->isValid()) {
            throw new AM_Model_Db_IssueHelpPage_Data_Exception($oUploadHandler->getMessagesAsString());
        }

        $aFiles         = $oUploadHandler->getFileInfo();
        $sFileExtension = strtolower(pathinfo($aFiles[$sInputName]['name'], PATHINFO_EXTENSION));
        $sDestination   = $this->_sResourceDir . DIRECTORY_SEPARATOR . $this->_sResourceName . '.' . $sFileExtension;
        //Rename file and receive it
        $oUploadHandler->addFilter('Rename', array('target' => $sDestination, 'overwrite' => true));

        if (!$oUploadHandler->receive($sInputName)) {
            throw new AM_Model_Db_IssueHelpPage_Data_Exception($oUploadHandler->getMessagesAsString());
        }

        $this->_sResourceDbBaseName = $aFiles[$sInputName]['name'];

        $this->_postUpload($sDestination);

        return $this;
    }

    /**
     * Post upload trigger
     * @param string $sDestination
     * @return AM_Model_Db_IssueHelpPage_Data_Resource
     */
    protected function _postUpload($sDestination)
    {
        if (AM_Tools::isAllowedImageExtension($sDestination)) {
            $oThumbnailerHandler = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
            /* @var $oThumbnailerHandler AM_Handler_Thumbnail */
            $oThumbnailerHandler->clearSources()
                    ->addSourceFile($sDestination)
                    ->loadAllPresets($this->_getIssueHelpPage()->getThumbnailPresetType())
                    ->createThumbnails();
        }

        return $this;
    }

    /**
     * Get first page of PDF file and conver it the PNG
     * @return string
     * @throws AM_Model_Db_IssueHelpPage_Data_Exception
     */
    public function getFirstPageAsPng()
    {
        $sResource = $this->_sResourceDir  . DIRECTORY_SEPARATOR . $this->getResourceBaseName();

        $oImageSource = AM_Resource_Factory::create($sResource);

        if (! $oImageSource instanceof AM_Resource_Concrete_Pdf) {
            throw new AM_Model_Db_IssueHelpPage_Data_Exception(sptintf('Wrong resource given "%s". Must be a PDF resource', $sResource));
        }

        $sFirstPagePath = $oImageSource->getFileForThumbnail();

        return $sFirstPagePath;
    }

    /**
     * Returns path to resource for API
     *
     * @param string $sKey
     * @return boolean|string
     */
    public function getResourcePathForExport()
    {
        $sFileExtension = pathinfo($this->_getIssueHelpPage()->name, PATHINFO_EXTENSION);
        $sFileExtension = Zend_Filter::filterStatic($sFileExtension, 'StringToLower', array('encoding' => 'UTF-8'));
        $sFileExtension = ('pdf' == $sFileExtension) ? 'png' : $sFileExtension;

        if (empty($sFileExtension)) {
            return null;
        }

        $sValue =   '/' . $this->_getIssueHelpPage()->getThumbnailPresetType()
                 . '/' . AM_Tools_String::generatePathFromId($this->_getIssueHelpPage()->id_issue)
                 . '/' . $this->_getIssueHelpPage()->type . '.' . $sFileExtension;

        return $sValue;
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
        AM_Tools::clearContent($this->_getIssueHelpPage()->getThumbnailPresetType(), $this->_getIssueHelpPage()->id_issue);
        AM_Tools::clearResizerCache($this->_getIssueHelpPage()->getThumbnailPresetType(), $this->_getIssueHelpPage()->id_issue);
    }
}
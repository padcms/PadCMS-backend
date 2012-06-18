<?php
/**
 * @file
 * AM_Resource_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Resource
 */
abstract class AM_Resource_Abstract implements AM_Resource_Interface
{
    const RESOURCE_CONCRETE_CLASS_PREFIX = 'AM_Resource_Concrete_';

    protected static $_allowedImageExtensoins = array('gif', 'png', 'jpg', 'jpeg', 'pdf');

    protected $_sSourceFile          = null; /**< @type string */
    protected $_sSourceFileExtension = null; /**< @type string */
    protected $_sSourceFileName      = null; /**< @type string */
    protected $_sSourceFileDir       = null; /**< @type string */
    /** @var Zend_Config **/
    protected $_oConfig = null; /**< @type Zend_Config */

    /**
     * @param string $sSourceFilePath
     */
    public final function __construct($sSourceFilePath)
    {
        $this->_setSourceFile($sSourceFilePath);
        $this->_init();
    }

    /**
     * Image source init
     */
    protected function _init()
    { }

    /**
     * Set path to image file
     * @param string $sSourceFilePath path to image file
     * @return AM_Resource_Abstract
     * @throws AM_Resource_Exception
     */
    protected function _setSourceFile($sSourceFilePath)
    {
        if (!AM_Tools_Standard::getInstance()->is_file($sSourceFilePath)) {
            throw new AM_Resource_Exception(sprintf('File \'%s\' not exists or has wrong type', $sSourceFilePath), 501);
        }

        $this->_sSourceFile = $sSourceFilePath;

        $info = pathinfo($this->_sSourceFile);
        $this->_sSourceFileExtension = strtolower($info['extension']);
        $this->_sSourceFileName      = $info['filename'];
        $this->_sSourceFileDir       = $info['dirname'];

        return $this;
    }

    /**
     * Get file wich will be resized for thumbnail
     * @return string Path to file
     */
    public function getFileForThumbnail()
    {
        return $this->_sSourceFile;
    }

    /**
     * @see AM_Image_Source_Interface::getSourceFile
     */
    public final function getSourceFile()
    {
        return $this->_sSourceFile;
    }

    /**
     * @see AM_Image_Source_Interface::getSourceFileDir
     */
    public final function getSourceFileDir()
    {
        return $this->_sSourceFileDir;
    }

    /**
     * @see AM_Image_Source_Interface::getSourceFileName
     */
    public final function getSourceFileName()
    {
        return $this->_sSourceFileName;
    }

    /**
     * @see AM_Image_Source_Interface::getSourceFileExtension
     */
    public final function getSourceFileExtension()
    {
        return $this->_sSourceFileExtension;
    }

    /**
     * Returns instnace of the system configuration
     * @return Zend_Config
     */
    public final function getConfig()
    {
        if (is_null($this->_oConfig)) {
            $this->_oConfig = Zend_Registry::get('config');
        }

        return $this->_oConfig;
    }

    /**
     * Checks is file has PDF extension
     *
     * @return boolean
     */
    public final function isPdf()
    {
        $bResult = ('pdf' == $this->getSourceFileExtension()) ? true : false;

        return $bResult;
    }

    /**
     * Checks is file has image extension
     *
     * @return boolean
     */
    public final function isImage()
    {
        $bResult = in_array($this->getSourceFileExtension(), self::$_allowedImageExtensoins);

        return $bResult;
    }
}
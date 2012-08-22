<?php
/**
 * @file
 * AM_Resource_Abstract class definition.
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
     * @param string $sImageType set what image type (png, jpg) we have to get
     * @return string Path to file
     */
    public function getFileForThumbnail($sImageType = null)
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
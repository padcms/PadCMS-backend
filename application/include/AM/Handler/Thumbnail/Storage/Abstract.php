<?php

/*
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
abstract class AM_Handler_Thumbnail_Storage_Abstract implements AM_Handler_Thumbnail_Storage_Interface
{
    /** @var array */
    protected $_aResources = array(); /**< @type array */
    /** @var AM_Handler_Thumbnail */
    protected $_oThumbnailHandler = null; /**<@type AM_Handler_Thumbnail */
    /** @var Zend_Config */
    protected $_oConfig = null; /**<@type Zend_Config */
    /** @var string */
    protected $_sPathPrefix = null; /**<@type string */

    /**
     * @param AM_Handler_Abstract $oThumbnailHandler
     * @param Zend_Config $oConfig
     */
    public function __construct(AM_Handler_Abstract $oThumbnailHandler, Zend_Config $oConfig)
    {
        $this->_oThumbnailHandler = $oThumbnailHandler;
        $this->_oConfig           = $oConfig;

        $this->_init();
    }

    /**
     * Init the storage
     */
    protected function _init()
    { }

    /**
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->_oConfig;
    }

    /**
     * @param type $sSourcePathPrefix
     * @return AM_Handler_Thumbnail_Storage_Abstract
     */
    public function setPathPrefix($sSourcePathPrefix)
    {
        $this->_sPathPrefix = trim($sSourcePathPrefix, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * Returns path prefix. Usually it is part of path with element id (00/00/01)
     * @return string
     */
    public function getPathPrefix()
    {
        return $this->_sPathPrefix;
    }

    /**
     * Add resource to the stack
     * @param string $sResourcePath
     * @return AM_Handler_Thumbnail_Storage_Abstract
     * @throws AM_Handler_Thumbnail_Storage_Exception
     */
    public function addResource($sResourcePath)
    {
        if (!AM_Tools_Standard::getInstance()->is_file($sResourcePath)) {
            throw new AM_Handler_Thumbnail_Storage_Exception(sprintf('Can\'t read resource \"%s\"', $sResourcePath));
        }

        $this->_aResources[] = $sResourcePath;

        return $this;
    }

    /**
     * @return array
     */
    public function getResources()
    {
        return $this->_aResources;
    }
}
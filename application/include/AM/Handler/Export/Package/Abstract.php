<?php
/**
 * @file
 * AM_Handler_Export_Package_Abstract class definition.
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
 * Abstract class: the base class of revision package
 *
 * @ingroup AM_Handler
 */
abstract class AM_Handler_Export_Package_Abstract implements AM_Handler_Export_Package_Interface
{
    /** @var string The name of the package. This name used to save package to disk or other place */
    protected $_sPackageName         = null; /**< @type string */
    /** @var string The name of downloaded package */
    protected $_sPackageDownloadName = null; /**< @type string */
    /** @var string The package path */
    protected $_sPackagePath         = null; /**< @type string */
    /** @var array The files inside the package */
    protected $_aFiles               = array(); /**< @type array */
    /** @var AM_Handler_Abstract **/
    protected $_oHandler             = null;

    /**
     * @param AM_Handler_Abstract $oHandler
     * @param string $sName The name of package
     */
    public function __construct(AM_Handler_Abstract $oHandler, $sName = null)
    {
        $this->setHandler($oHandler);

        if (!is_null($sName)) {
            $this->setPackageName($sName);
        }
    }

    /**
     * Set handler
     *
     * @param AM_Handler_Abstract $oHandler
     * @return AM_Handler_Export_Storage_Abstract
     */
    public function setHandler(AM_Handler_Abstract $oHandler)
    {
        $this->_oHandler = $oHandler;

        return $this;
    }

    /**
     * Return handler
     *
     * @return AM_Handler_Abstract
     */
    public function getHandler()
    {
        return $this->_oHandler;
    }

    /**
     * Add file resource to the package
     *
     * @see AM_Handler_Export_Package_Interface::addFile()
     * @param string $sPathReal
     * @param string $sPathInPackage
     * @return AM_Handler_Export_Package_Abstract
     * @throws AM_Handler_Export_Package_Exception
     */
    public function addFile($sPathReal, $sPathInPackage)
    {
        if (!AM_Tools_Standard::getInstance()->file_exists($sPathReal)) {
            throw new AM_Handler_Export_Package_Exception(sprintf('File "%s" doesn\'t exist', $sPathReal));
        }

        if (empty($sPathInPackage)) {
            throw new AM_Handler_Export_Package_Exception('File path in package have to be not empty');
        }

        $this->_aFiles[$sPathReal] = $sPathInPackage;

        return $this;
    }

    /**
     * @see AM_Handler_Export_Package_Interface::getPackagePath()
     * @return string|null
     */
    public function getPackagePath()
    {
        return $this->_sPackagePath;
    }

    /**
     * @see AM_Handler_Export_Package_Interface::setPackageName()
     * @param string $sName
     * @return AM_Handler_Export_Package_Abstract
     */
    public function setPackageName($sName)
    {
        $this->_sPackageName = $sName;

        return $this;
    }

    /**
     * @see AM_Handler_Export_Package_Interface::setPackageDownloadName()
     * @param string $sName
     * @return AM_Handler_Export_Package_Abstract
     */
    public function setPackageDownloadName($sName)
    {
        $this->_sPackageDownloadName = $sName;

        return $this;
    }

    /**
     * Remove files from package stack
     * @return AM_Handler_Export_Package_Abstract
     */
    public function reset()
    {
        $this->_aFiles = array();

        return $this;
    }
}
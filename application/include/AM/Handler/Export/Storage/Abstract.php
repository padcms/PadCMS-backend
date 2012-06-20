<?php
/**
 * @file
 * AM_Handler_Export_Storage_Abstract class definition.
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
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Abstract class: the base class of revision package storages
 * This class is responsible for keeping the package
 *
 * @ingroup AM_Handler
 */
abstract class AM_Handler_Export_Storage_Abstract implements AM_Handler_Export_Storage_Interface
{
    /** @var AM_Handler_Export_Package_Abstract **/
    protected $_oPackage = null; /**< @type AM_Handler_Export_Package_Abstract */
    /** @var AM_Handler_Abstract **/
    protected $_oHandler = null; /**< @type AM_Handler_Abstract */
    /** @var string The path prefix (usually this is a path like 00/00/00/01, where 1 - revision id) **/
    protected $_sPathPrefix = null; /**< @type string */

    public function __construct(AM_Handler_Abstract $oHandler)
    {
        $this->setHandler($oHandler);
    }

    /**
     * Add package to the storage
     * @param AM_Handler_Export_Package_Abstract $oPackage
     * @return AM_Handler_Export_Storage_Abstract
     * @throws AM_Handler_Export_Storage_Exception
     */
    public final function addPackage(AM_Handler_Export_Package_Abstract $oPackage)
    {
        $this->_oPackage = $oPackage;

        return $this;
    }

    /**
     * Get package
     * @return AM_Handler_Export_Package_Abstract
     */
    public function getPackage()
    {
        return $this->_oPackage;
    }

    /**
     * Set handler
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
     * @return AM_Handler_Abstract
     */
    public function getHandler()
    {
        return $this->_oHandler;
    }

    /**
     * Set path prefix (usually path 00/00/00/01)
     * @param string $sPathPrefix
     * @return AM_Handler_Export_Storage_Abstract
     */
    public function setPathPrefix($sPathPrefix)
    {
        $this->_sPathPrefix = $sPathPrefix;

        return $this;
    }
}
<?php
/**
 * @file
 * AM_Handler_Export_Abstract class definition.
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
 * Abstract class: the base class of export handler
 * This class is responsible for exporting revision data to the package and sending package to the third-party application
 *
 * @ingroup AM_Handler
 */
abstract class AM_Handler_Export_Abstract extends AM_Handler_Abstract
{
    /** @var AM_Model_Db_Revision **/
    protected $_oRevision    = null; /**< @type AM_Model_Db_Revision */
    /** @var AM_Handler_Export_Package_Abstract **/
    protected $_oPackage     = null; /**< @type AM_Handler_Export_Package_Abstract */
    /** @var AM_Handler_Export_Storage_Abstract **/
    protected $_oStorage     = null; /**< @type AM_Handler_Export_Storage_Abstract */

    public function __construct()
    {
        $this->setPackage(new AM_Handler_Export_Package_Zip($this));
        $this->setStorage(new AM_Handler_Export_Storage_Local($this));

        $this->getStorage()
                ->addPackage($this->getPackage());
    }

    /**
     * Send revision package
     * @param AM_Model_Db_Revision $oRevision
     * @param int|null $iIsContinue
     */
    public function sendRevisionPackage(AM_Model_Db_Revision $oRevision, $iIsContinue = null)
    {
        $this->getPackage()
                ->setPackageName($this->_getRevisionPackageName($oRevision))
                ->setPackageDownloadName($oRevision->id);

        $this->getStorage()
                ->setPathPrefix($this->_getPackagePathPrefix($oRevision))
                ->sendPackage($iIsContinue);
    }

    /**
     * Run export task
     * @param AM_Model_Db_Revision $oRevision
     * @return AM_Handler_Export
     * @throws AM_Handler_Export_Exception
     */
    public function initExportProcess(AM_Model_Db_Revision $oRevision)
    {
        $sCommand = sprintf('nohup %s/padcms export --revision=%d > /dev/null 2>&1 &', APPLICATION_PATH . DIRECTORY_SEPARATOR . '..', $oRevision->id);
        AM_Tools_Standard::getInstance()->exec($sCommand);

        return $this;
    }

    /**
     * Run application export task
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Handler_Export
     * @throws AM_Handler_Export_Exception
     */
    public function initApplicationExportProcess(AM_Model_Db_Application $oApplication)
    {
        $sCommand = sprintf('nohup %s/padcms export --application=%d > /dev/null 2>&1 &', APPLICATION_PATH . DIRECTORY_SEPARATOR . '..', $oApplication->id);
        AM_Tools_Standard::getInstance()->exec($sCommand);

        return $this;
    }

    /**
     * Create all archives for revision
     * (full archive, revision progressive archive, page archive)
     * @param AM_Model_Db_Revision $oRevision
     * @return AM_Handler_Export
     * @throws AM_Handler_Export_Exception
     */
    public function exportRevision(AM_Model_Db_Revision $oRevision)
    {
        $oProfiler = new AM_Tools_Profiler();
        $oProfiler->start();
        $this->getLogger()->debug(sprintf('Export revision "%s" [START]', $oRevision->id));

        //Prepeare new package
        $this->getPackage()
                ->reset()
                ->setPackageName($this->_getRevisionPackageName($oRevision));

        $this->_doExport($oRevision);

        //Move package to the store
        $this->getStorage()
                ->setPathPrefix($this->_getPackagePathPrefix($oRevision))
                ->savePackage();

        $oProfiler->finish();
        $this->getLogger()->debug(sprintf('Export revision "%s" [FINISH] [%s]', $oRevision->id, $oProfiler->getExecutionTime()));

        return $this;
    }

    /**
     * Return package
     * @return AM_Handler_Export_Package_Abstract
     * @throws AM_Handler_Export_Exception
     */
    public function getPackage()
    {
        if (is_null($this->_oPackage)) {
            throw new AM_Handler_Export_Exception('Try to set package before get it');
        }

        return $this->_oPackage;
    }

    /**
     * Set package instance
     * @param AM_Handler_Export_Package_Abstract $oPackage
     * @return AM_Handler_Export
     * @throws AM_Handler_Export_Exception
     */
    public function setPackage($oPackage)
    {
        if (!$oPackage instanceof AM_Handler_Export_Package_Abstract) {
            throw new AM_Handler_Export_Exception('Given data must be a "AM_Handler_Export_Package_Abstract"', 500);
        }

        $this->_oPackage = $oPackage;

        return $this;
    }

    /**
     * Get export storage
     * @return AM_Handler_Export_Storage_Abstract
     * @throws AM_Handler_Export_Exception
     */
    public function getStorage()
    {
        if (is_null($this->_oStorage)) {
            throw new AM_Handler_Export_Exception('Try to set storage before get it');
        }

        return $this->_oStorage;
    }

    /**
     * Set export storage
     * @param AM_Handler_Export_Storage_Abstract $oStorage
     * @return AM_Handler_Export
     * @throws AM_Handler_Export_Exception
     */
    public function setStorage($oStorage)
    {
        if (!$oStorage instanceof AM_Handler_Export_Storage_Abstract) {
            throw new AM_Handler_Export_Exception('Given data must be a "AM_Handler_Export_Storage_Abstract"', 500);
        }

        $this->_oStorage = $oStorage;

        return $this;
    }

    /**
     * @todo: move this to a better place
     * @param $oRevision AM_Model_Db_Revision
     * @return string
     */
    protected function _getRevisionPackageName(AM_Model_Db_Revision $oRevision)
    {
        $sName = 'revission-' . $oRevision->id;

        return $sName;
    }

    /**
     * Get path prefix (usually 00/00/01/01, where 101 - revision id)
     * @param AM_Model_Db_Revision $oRevision
     * @return string
     */
    protected function _getPackagePathPrefix(AM_Model_Db_Revision $oRevision)
    {
        $sPath = AM_Tools_String::generatePathFromId($oRevision->id);

        return $sPath;
    }
}
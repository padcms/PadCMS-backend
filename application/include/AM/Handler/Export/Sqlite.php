<?php
/**
 * @file
 * AM_Handler_Export_Sqlite class definition.
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
 * Sqlite implementation of export handler
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Export_Sqlite extends AM_Handler_Export_Abstract
{
    /** @var string The path to the sqlite DB file */
    protected $_sDbFile = null; /**< @type string */
    /** @var Zend_Db_Adapter_Abstract **/
    protected $_oAdapter = null; /**< @type Zend_Db_Adapter_Abstract */

    /**
     * Export to sqlite
     *
     * @param AM_Model_Db_Revision $oRevision
     * @return AM_Handler_Export_Sqlite
     */
    protected function _doExport(AM_Model_Db_Revision $oRevision)
    {
        $this->_reset();
        //Export horisontal pages
        $this->_exportPagesHorisontal($oRevision);
        //Export Pages
        $this->_exportPages($oRevision);
        //Export Menu
        $this->_exportMenu($oRevision);

        $this->_addDbToPackage();

        return $this;
    }

    /**
     * Get revision's pages and export them to the manifest and package
     *
     * @param AM_Model_Db_Revision $oRevision
     * @return AM_Handler_Export_Sqlite
     */
    protected function _exportPages(AM_Model_Db_Revision $oRevision)
    {
        $oPages = $oRevision->getPages();
        foreach ($oPages as $oPage) {
            /* @var $oPage AM_Model_Db_Page */
            $oMapper = $this->_getMapper($oPage);
            /* @var $oMapper AM_Mapper_Sqlite_Page */
            $oMapper->unmap();

            //Exporting page imposition
            //TODO: refactoring. get all impositions by single query
            $oPageImposition = AM_Model_Db_Table_Abstract::factory('page_imposition')->findOneBy('is_linked_to', $oPage->id);
            if (!is_null($oPageImposition)) {
                $oMapper = $this->_getMapper($oPageImposition);
                /* @var $oMapper AM_Mapper_Sqlite_PageImposition */
                $oMapper->unmap();
            }

            //Export elements
            $oElements = $oPage->getElements();
            foreach ($oElements as $oElement) {
                $oMapper = $this->_getMapper($oElement);
                /* @var $oMapper AM_Mapper_Sqlite_Element */
                $oMapper->unmap();
            }
        }

        return $this;
    }

    /**
     * Export menu
     *
     * @param AM_Model_Db_Revision $oRevision
     * @return AM_Handler_Export_Sqlite
     */
    protected function _exportMenu(AM_Model_Db_Revision $oRevision)
    {
        $aTerm = AM_Model_Db_Table_Abstract::factory('term')->getTocAsList($oRevision);

        foreach ($aTerm as $iTermId => $sTermTitle) {
            $oTerm = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(array('id' => $iTermId));
            if (is_null($oTerm)) {
                continue;
            }
            $oTerm->title = $sTermTitle;
            $oMapper = $this->_getMapper($oTerm);
            /* @var $oMapper AM_Mapper_Sqlite_Term */
            $oMapper->unmap();
        }

        return $this;
    }

    /**
     * Export horisontal pages
     *
     * @param AM_Model_Db_Revision $oRevision
     * @return AM_Handler_Export_Sqlite
     */
    protected function _exportPagesHorisontal(AM_Model_Db_Revision $oRevision)
    {
        $oPagesHorisontal = AM_Model_Db_Table_Abstract::factory('page_horisontal')
                ->findAllBy(array('id_issue' => $oRevision->getIssue()->id), null, 'weight');

        foreach ($oPagesHorisontal as $oPageHorisontal) {
            $oMapper = $this->_getMapper($oPageHorisontal);
            /* @var $oMapper AM_Mapper_Sqlite_PageHorisontal */
            $oMapper->unmap();
        }

        return $this;
    }

    /**
     * Returns path of the DB file
     *
     * @return string File path
     */
    protected function _getDbFile()
    {
        if (is_null($this->_sDbFile)) {
            $this->_sDbFile = AM_Handler_Temp::getInstance()->getFile('revision.db');
        }

        return $this->_sDbFile;
    }

    /**
     * Reset the coonection adapter
     * Need for massexporting
     * @return AM_Handler_Export_Sqlite
     */
    protected function _reset()
    {
        $this->_sDbFile  = null;
        $this->_oAdapter = null;
        return $this;
    }

    /**
     * Establish connection and create DB
     *
     * @return Zend_Db_Adapter_Pdo_Sqlite
     * @throws AM_Handler_Export_Sqlite_Exception
     */
    public function getAdapter()
    {
        if (is_null($this->_oAdapter)) {
            $this->_oAdapter = Zend_Db::factory('PDO_SQLITE', array('dbname' => $this->_getDbFile()));

            $sSchema = @file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Sqlite' . DIRECTORY_SEPARATOR . 'schema.sqlite.sql');
            if (empty ($sSchema)) {
                throw new AM_Handler_Export_Sqlite_Exception('Schema file is empty');
            }

            $this->_oAdapter->getConnection()->exec($sSchema);

            AM_Tools_Standard::getInstance()->chmod($this->_getDbFile(), 0666);
        }

        return $this->_oAdapter;
    }

    /**
     * Add DB to the package
     *
     * @return AM_Handler_Export_Sqlite
     */
    protected function _addDbToPackage()
    {
        $this->getPackage()->addFile($this->_getDbFile(), 'sqlite.db');

        return $this;
    }

    /**
     * Returns mapper object for given model
     *
     * @param AM_Model_Db_Abstract $oModel
     * @return AM_Mapper_Sqlite_Abstract
     */
    protected function _getMapper(AM_Model_Db_Abstract $oModel)
    {
        $oMapper = AM_Mapper_Abstract::factory($oModel, 'sqlite', array('adapter' => $this->getAdapter()));

        return $oMapper;
    }
}
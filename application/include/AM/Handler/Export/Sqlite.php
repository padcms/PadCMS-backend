<?php
/**
 * @file
 * AM_Handler_Export_Sqlite class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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
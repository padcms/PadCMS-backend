<?php
/**
 * @file
 * AM_Cli_Task_Build class definition.
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
 * This task builds builds DB tables and clean all resources
 * @ingroup AM_Cli
 */
class AM_Cli_Task_Build extends AM_Cli_Task_Abstract
{
    /** @var Zend_Db_Adapter_Abstract **/
    protected $_oDbAdapter; /**< @type Zend_Db_Adapter_Abstract */

    protected function _configure()
    { }

    public function execute()
    {
        define('DEPLOY_FOLDER', APPLICATION_PATH
                                . DIRECTORY_SEPARATOR
                                . '..'
                                . DIRECTORY_SEPARATOR
                                . 'deploy');

        $this->_oDbAdapter = Zend_Registry::get('db');
        $dbShemaPath = DEPLOY_FOLDER
                . DIRECTORY_SEPARATOR . 'db'
                . DIRECTORY_SEPARATOR . 'schema';

        $dbFixturesPath = DEPLOY_FOLDER
                . DIRECTORY_SEPARATOR . 'db'
                . DIRECTORY_SEPARATOR . 'fixtures';


        $this->_loadShemas($dbShemaPath);
        $this->_loadFixtures($dbFixturesPath);

        //It is dangerous for production
        //@todo: ctreate task for resource cleaning
//        AM_Tools::clearContent();
//        AM_Tools::clearResizerCache();
    }

    /**
     * Find sql files in shemas path and execute SQL queries
     *
     * @param string $sPath
     */
    protected function _loadShemas($sPath)
    {
        $aSchemaFiles = AM_Tools_Finder::type('file')
                ->name('*.sql')
                ->in($sPath);

        $this->_echo('Loading shemas');
        foreach ($aSchemaFiles as $sSchemaFilePath) {
            $sSql  = file_get_contents($sSchemaFilePath);
            $sSql  = str_replace('\n', '', $sSql);
            $sSql  = rtrim($sSql, ';');
            $aSqls = explode(';', $sSql);

            $bHasError = false;
            foreach ($aSqls as $sSql) {
                try {
                    $this->_oDbAdapter->query($sSql);
                } catch (Zend_Db_Statement_Exception $oException){
                    $bHasError = true;
                    $this->_echo($sSchemaFilePath, 'error');
                    $this->_echo($oException->getMessage(), 'error');
                }
            }

            if (!$bHasError) {
                $this->_echo($sSchemaFilePath, 'success');
            }
        }
    }

    /**
     * Find sql files in fixtures path and execute SQL queries
     *
     * @param string $sPath
     */
    protected function _loadFixtures($sPath)
    {
        $aSchemaFiles = AM_Tools_Finder::type('file')
                ->name('*.sql')
                ->in($sPath);

        $this->_echo('Loading fixtures');
        foreach ($aSchemaFiles as $sSchemaFilePath) {
            $sSql = file_get_contents($sSchemaFilePath);
            $sSql = str_replace('\n', '', $sSql);
            $sSql  = rtrim($sSql, ';');
            $aSqls = explode(';', $sSql);

            $bHasError = false;
            foreach ($aSqls as $sSql) {
                try {
                    $this->_oDbAdapter->query($sSql);
                } catch (Zend_Db_Statement_Exception $oException){
                    $bHasError = true;
                    $this->_echo($sSchemaFilePath, 'error');
                    $this->_echo($oException->getMessage(), 'error');
                }
            }

            if (!$bHasError) {
                $this->_echo('Loading fixture: ' . $sSchemaFilePath, 'success');
            }
        }
    }
}
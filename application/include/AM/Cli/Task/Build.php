<?php
/**
 * @file
 * AM_Cli_Task_Build class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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
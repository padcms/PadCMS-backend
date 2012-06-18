<?php
/**
 * @file
 * AM_Test_PHPUnit_DatabaseTestCase class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Test
 */

/**
 * Base DB test case
 * @ingroup AM_Test
 */
abstract class AM_Test_PHPUnit_DatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /** @var Zend_Test_PHPUnit_Db_Connection */
    private $_oConnection = null; /**< @type Zend_Test_PHPUnit_Db_Connection */

    /**
     * Returns path to YML file with fixtures
     * @return null
     */
    protected function _getDataSetYmlFile()
    {
        return null;
    }

    /**
     * Returns array with fixtures
     * @return array
     */
    protected function _getDataSetArray()
    {
        return array();
    }

    /**
     * Returns the test dataset
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        if (!is_null($this->_getDataSetYmlFile())) {
            $oDataSet = $this->createYamlDataSet($this->_getDataSetYmlFile());

            return $oDataSet;
        }

        $aDataSetArray = (array) $this->_getDataSetArray();
        $oDataSet      = $this->getConnection()->createDataSet($aDataSetArray);

        return $oDataSet;
    }

     /**
     * Creates a new YamlDataSet with the given $ymlFile. (absolute path.)
     *
     * @param string $yamlFile
     * @return PHPUnit_Extensions_Database_DataSet_YamlDataSet
     */
    protected function createYamlDataSet($yamlFile)
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet($yamlFile);
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if (is_null($this->_oConnection)) {
            /* @var $oDbAdapter Zend_Db_Adapter_Abstract */
            $oDbAdapter = Zend_Registry::get('db');

            $aDbConfiguration = $oDbAdapter->getConfig();

            $this->_oConnection = $this->createZendDbConnection($oDbAdapter, $aDbConfiguration['dbname']);
            Zend_Db_Table_Abstract::setDefaultAdapter($oDbAdapter);
        }

        return $this->_oConnection;
    }

    /**
     * Returns the database operation executed in test setup.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getSetUpOperation()
    {
        $aOperations = array(new Zend_Test_PHPUnit_Db_Operation_Truncate());

        if (!is_null($this->_getDataSetYmlFile())) {
            $aOperations[] = new Zend_Test_PHPUnit_Db_Operation_Insert();
        }

        return new PHPUnit_Extensions_Database_Operation_Composite($aOperations);
    }

    /**
     * Asserts that two given tables are equal.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $oExpected
     * @param PHPUnit_Extensions_Database_DataSet_ITable $oActual
     * @param string $sMessage
     */
    public static function assertTablesEqual(PHPUnit_Extensions_Database_DataSet_ITable $oExpected, PHPUnit_Extensions_Database_DataSet_ITable $oActual, $sMessage = '')
    {
        $oConstraint = new AM_Test_PHPUnit_Database_Constraint_TableIsEqual($oExpected);

        self::assertThat($oActual, $oConstraint, $sMessage);
    }

    /**
     * Asserts that two given datasets are equal.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $oExpected
     * @param PHPUnit_Extensions_Database_DataSet_ITable $oActual
     * @param string $sMessage
     */
    public static function assertDataSetsEqual(PHPUnit_Extensions_Database_DataSet_IDataSet $oExpected, PHPUnit_Extensions_Database_DataSet_IDataSet $oActual, $sMessage = '')
    {
        $oConstraint = new AM_Test_PHPUnit_Database_Constraint_DataSetIsEqual($oExpected);

        self::assertThat($oActual, $oConstraint, $sMessage);
    }
}

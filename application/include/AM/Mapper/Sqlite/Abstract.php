<?php
/**
 * @file
 * AM_Mapper_Sqlite_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Mapper
 */
abstract class AM_Mapper_Sqlite_Abstract extends AM_Mapper_Abstract
{
    /** Zend_Db_Adapter_Pdo_Sqlite **/
    protected $_oAdapter = null;

    protected $_aUseAttributesForUnmap = array();

    /**
     * Prepeare sqlite adapter
     *
     * @param array $aOptions
     * @return AM_Mapper_Sqlite_Abstract
     * @throws AM_Mapper_Sqlite_Exception
     */
    protected function _init($aOptions = array())
    {
        if (!array_key_exists('adapter', $aOptions)) {
            throw new AM_Mapper_Sqlite_Exception('Parameter "adapter" is empty');
        }

        $this->_oAdapter = $aOptions['adapter'];

        if (!$this->_oAdapter instanceof Zend_Db_Adapter_Pdo_Sqlite) {
            throw new AM_Mapper_Sqlite_Exception(sprintf('Wrong adapter given. "Zend_Db_Adapter_Pdo_Sqlite" expected, but "%s" given', get_class($this->_oAdapter)));
        }

        return $this;
    }

    /**
     * Unmaps model to Sqlite
     *
     * @return AM_Mapper_Sqlite_Abstract
     */
    public function unmap()
    {
        $this->_unmapCustom();

        return $this;
    }

    /**
     * Implementation of additional operations
     * @return AM_Mapper_Xml_Abstract
     */
    protected function _unmapCustom()
    {
        return $this;
    }

    /**
     * Returns gateway to connect to sqlite data source
     *
     * @param string $sName
     * @return Zend_Db_Table
     * @throws AM_Mapper_Sqlite_Exception
     */
    protected function _getSqliteGateway($sName = null)
    {
        $sName   = is_null($sName)? $this->_getSqliteTabletName() : $sName;
        $aConfig = array(Zend_Db_Table_Abstract::ADAPTER => $this->_getAdapter(),
                         Zend_Db_Table_Abstract::NAME    => $sName);
        try {
            $oTable = new Zend_Db_Table($aConfig);
        } catch (Exception $oException) {
            throw new AM_Mapper_Sqlite_Exception('Can\'t get sqlite gateway. ' . $oException->getMessage());
        }

        return $oTable;
    }

    /**
     * Get sqlite table name. It often is equal to the model table name
     *
     * @return string Table name
     */
    protected function _getSqliteTabletName()
    {
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_Word_CamelCaseToUnderscore())
               ->addFilter(new Zend_Filter_StringToLower());
        $xmlElement = $filter->filter($this->_getModelName());

        return $xmlElement;
    }

    /**
     * @return Zend_Db_Adapter_Pdo_Sqlite
     */
    protected function _getAdapter()
    {
        return $this->_oAdapter;
    }
}
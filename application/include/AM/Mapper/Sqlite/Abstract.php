<?php
/**
 * @file
 * AM_Mapper_Sqlite_Abstract class definition.
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
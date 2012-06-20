<?php
/**
 * @file
 * AM_Model_Db_Abstract class definition.
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
 * @defgroup AM_Model
 */

/**
 * The base model class
 * @ingroup AM_Model
 */
abstract class AM_Model_Db_Abstract extends Zend_Db_Table_Row_Abstract
{
    const TABLE_CLASS_PREFIX  = 'AM_Model_Db_Table';
    const ROW_CLASS_PREFIX    = 'AM_Model_Db';
    const ROWSET_CLASS_PREFIX = 'AM_Model_Db_Rowset';

    /**
     * @param array $aConfig
     */
    public function __construct(array $aConfig = array())
    {
        $this->_tableClass = $this->_getTableClass();
        parent::__construct($aConfig);
    }

    /**
     * Initilization
     */
    protected function _init()
    { }

    /**
     * @see Zend_Db_Table_Row_Abstract::init()
     */
    public function init()
    {
        //Init _data array with table colums as keys
        $aColumnsAsValues = $this->_getTableColumns();
        $aColumnsAsKeys   = array_fill_keys($aColumnsAsValues, null);
        if (empty($this->_data)) {
            $this->_data = $aColumnsAsKeys;
        } else {
            $this->_data    = array_merge($aColumnsAsKeys, $this->_data);
        }

        $this->_init();
    }

    /**
     * Get model table columns
     * @return array
     */
    protected function _getTableColumns()
    {
        $aColumns = $this->getTable()->info(Zend_Db_Table_Abstract::COLS);

        return $aColumns;
    }

    /**
     * Returns $this->_data onlym with columns keys
     * @return array
     */
    public function toArray()
    {
        $aColumns = $this->_getTableColumns();
        $aData = array();

        foreach ($aColumns as $sColumnName) {
            $aData[$sColumnName] = $this->$sColumnName;
        }

        return $aData;
    }

    /**
     * Create new active record with current record data
     * @todo refactoring
     * @param array $aModifiedData The list of new values
     * @return AM_Model_Db_Abstract The copied object
     */
    public function copy($aModifiedData = array())
    {
        $aModifiedData = (array) $aModifiedData;

        $aData = $this->toArray();
        unset($aData['id']);

        $aData = array_replace($aData, (array) $aModifiedData);

        $aClassName = get_class($this);
        /* @var $oCopiedObject AM_Model_Db_Abstract */
        $oCopiedObject = new $aClassName();
        $oCopiedObject->setFromArray($aData);
        $oCopiedObject->save();

        $this->setFromArray($oCopiedObject->toArray());
        //Clear change history
        $this->_cleanData      = $this->_data;
        $this->_modifiedFields = array();

        return $oCopiedObject;
    }

    /**
     * Get table name
     * @return string
     * @throws AM_Model_Db_Exception
     */
    public function getTableName()
    {
        $sClassName = get_class($this);
        $aChunks    = explode('_', $sClassName);
        //Get the last part of the class name
        $sModelName = array_pop($aChunks);
        if (empty($sModelName)) {
            throw new AM_Model_Db_Exception(sprintf('Model class "%s" has wrong name', $sModelName), 501);
        }
        $oFilter = new Zend_Filter();
        $oFilter->addFilter(new Zend_Filter_Word_CamelCaseToUnderscore())
               ->addFilter(new Zend_Filter_StringToLower());
        $sModelName = $oFilter->filter($sModelName);

        return $sModelName;
    }

    /**
     * Get table class name
     * @return string
     * @throws AM_Model_Db_Exception
     */
    protected function _getTableClass()
    {
        $sClassName      = get_class($this);
        $aChunks         = explode('_', $sClassName);
        $sTableClassName = array_pop($aChunks);
        if (empty($sTableClassName)) {
            throw new AM_Model_Db_Exception(sprintf('Model class "%s" has wrong name', $sClassName), 501);
        }
        $sTableClassName = self::TABLE_CLASS_PREFIX . '_' . $sTableClassName;

        return $sTableClassName;
    }

    /**
     * Fills object properties from array
     *
     * @param  array $aData
     * @return AM_Model_Db_Abstract
     */
    public function setFromArray(array $aData)
    {
        $aData = array_intersect_key($aData, $this->_data);

        foreach ($aData as $sColumnName => $sValue) {
            $this->__set($sColumnName, $sValue);
        }

        return $this;
    }

    /**
     * Check if record is new
     * @return boolean
     */
    public function isNew()
    {
        /**
         * If the _cleanData array is empty,
         * this is an INSERT of a new row.
         * Otherwise it is an UPDATE.
         */
        $bIsNew = empty($this->_cleanData);

        return $bIsNew;
    }

    /**
     * Set row field value
     *
     * @param  string $sColumnName The column key.
     * @param  mixed  $sValue      The value for the property.
     * @return void
     * @throws AM_Model_Db_Exception
     */
    public function __set($sColumnName, $sValue)
    {
        $sColumnName = $this->_transformColumn($sColumnName);
        if (!array_key_exists($sColumnName, $this->_data)) {
            throw new AM_Model_Db_Exception(sprintf('Specified column "%s" is not in the row', $sColumnName));
        }

        //Filter value by user defined callback
        $sCallbackName = 'filterValue' . Zend_Filter::filterStatic($sColumnName, 'Word_UnderscoreToCamelCase');
        if (method_exists($this, $sCallbackName)) {
            $sValue = $this->$sCallbackName($sValue);
        }

        $this->_data[$sColumnName]           = $sValue;
        $this->_modifiedFields[$sColumnName] = true;
    }
}
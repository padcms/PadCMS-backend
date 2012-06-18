<?php
/**
 * @file
 * AM_Model_Db_Rowset_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The base rowset class
 * @ingroup AM_Model
 */
abstract class AM_Model_Db_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract
{
    /**
     * @see Zend_Db_Table_Rowset_Abstract::init()
     */
    public function init()
    {
        $this->_rowClass = AM_Model_Db_Abstract::ROW_CLASS_PREFIX . '_' . $this->_getModelName();
    }

    /**
     * Gets model postfix
     *
     * @return void
     * @throws Base_Exception
     */
    protected function _getModelName()
    {
        $sClassName      = get_class($this);
        $aChunks         = explode('_', $sClassName);
        $sModelClassName = array_pop($aChunks);
        if (empty($sModelClassName)) {
            throw new AM_Model_Db_Exception(sprintf('Class "%s" has wrong name', $sClassName), 501);
        }

        return $sModelClassName;
    }

    /**
     * Add row object to rowset
     * @param Zend_Db_Table_Row_Abstract $oRow
     * @return AM_Model_Db_Rowset_Abstract
     * @throws AM_Model_Db_Rowset_Exception
     */
    public final function addRow(Zend_Db_Table_Row_Abstract $oRow)
    {
        if (!$oRow instanceof $this->_rowClass) {
            $sRowClassName  = get_class($oRow);
            throw new AM_Model_Db_Rowset_Exception(sprintf('Trying to add foreign row "%s" to rowset. Excepted "%s"', $sRowClassName, $this->_rowClass));
        }

        array_push($this->_data, $oRow->toArray());
        $this->_rows[$this->_count] = $oRow;

        $this->_count = count($this->_data);

        return $this;
    }

    /**
     * Delete rows
     * @return AM_Model_Db_Rowset_Abstract
     */
    public function delete()
    {
        foreach ($this as $oRow) {
            /* @var $oIssue AM_Model_Db_Abstract */
            $oRow->delete();
        }

        return $this;
    }
}
<?php
/**
 * @file
 * AM_Model_Db_Rowset_Abstract class definition.
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
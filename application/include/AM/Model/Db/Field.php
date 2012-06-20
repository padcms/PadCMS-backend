<?php
/**
 * @file
 * AM_Model_Db_Field class definition.
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
 * Field model class
 * Each page contains few layers (fields) with a certain type
 * @ingroup AM_Model
 */
class AM_Model_Db_Field extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_FieldType **/
    protected $_oFieldType = null;

    /**
     * Get field type
     * @return AM_Model_Db_FieldType
     */
    public function getFieldType()
    {
        if (is_null($this->_oFieldType)) {
            $this->fetchFieldType();
        }

        return $this->_oFieldType;
    }

    /**
     * Fetch field type
     * @return AM_Model_Db_Field
     */
    public function fetchFieldType()
    {
        $this->_oFieldType = AM_Model_Db_Table_Abstract::factory('field_type')->findOneBy('id', $this->field_type);

        if (is_null($this->_oFieldType)) {
            throw new AM_Model_Db_Exception(sprintf('Field "%d" has no type', $this->id));
        }

        return $this;
    }
}
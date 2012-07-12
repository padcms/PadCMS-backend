<?php
/**
 * @file
 * AM_Model_Db_ElementData class definition.
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
 * Element data model class
 * The 'element_data' table saves element's data in key-value format
 * The logic of work with this data is encapsulated in the AM_Model_Db_Element_Data_Abstract class
 * @ingroup AM_Model
 */
class AM_Model_Db_ElementData extends AM_Model_Db_Abstract
{

    /** @var AM_Model_Db_Element **/
    protected $_oElement = null; /**< @type AM_Model_Db_Element */
    /** @var AM_Model_Db_Element_Data_Abstract **/
    protected $_oData = null; /**< @type AM_Model_Db_Element_Data_Abstract */

    /**
     * Returns element
     * @return AM_Model_Db_Element
     */
    public function getElement()
    {
        if (is_null($this->_oElement)) {
            $this->_oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy(array('id' => $this->id_element));
        }

        return $this->_oElement;
    }

    /**
     * Returns resource object
     * @return AM_Model_Db_Element_Data_Abstract
     */
    public function getData()
    {
        if (is_null($this->_oData) && !is_null($this->getElement())) {
            $this->_oData = $this->getElement()->getResources();
        }

        return $this->_oData;
    }
}
<?php
/**
 * @file
 * AM_Model_Db_PageBackground class definition.
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
 * Page background model class
 * @ingroup AM_Model
 */
class AM_Model_Db_PageBackground extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_Element */
    protected $_oElement = null; /**< @type AM_Model_Db_Element */

    /**
     * Get data from current object, gives it a new page & element, insert new record
     * @param AM_Model_Db_Page $oPage
     * @return AM_Model_Db_PageBackground
     */
    public function copyToPage(AM_Model_Db_Page $oPage)
    {
        $oElement = $this->getElement();

        $aData            = array();
        $aData['page']    = $oPage->id;
        $aData['updated'] = null;
        $aData['id']      = $oElement->id;

        $this->copy($aData);

        return $this;
    }

    /**
     * Set element object
     * @param AM_Model_Db_Element $oElement
     * @return AM_Model_Db_PageBackground
     */
    public function setElement(AM_Model_Db_Element $oElement)
    {
        $this->_oElement = $oElement;

        return $this;
    }

    /**
     * Get element object
     * @return AM_Model_Db_Element | null
     */
    public function getElement()
    {
        if (empty($this->_oElement)) {
            $this->fetchElement();
        }

        if (empty($this->_oElement)) {
            throw new AM_Model_Db_Exception(sprintf('Page background "%s" has no element', $this->id));
        }

        return $this->_oElement;
    }

    /**
     * Fetch element object
     * @return AM_Model_Db_PageBackground
     */
    public function fetchElement()
    {
        $this->_oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', $this->id);

        return $this;
    }
}
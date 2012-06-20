<?php
/**
 * @file
 * AM_Model_Db_Element class definition.
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
 * Element model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Element extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_Element_Data_Abstract */
    protected $_oResources  = null; /**< @type AM_Model_Db_Element_Data_Abstract */
    /** @var AM_Model_Db_Page */
    protected $_oPage       = null; /**< @type AM_Model_Db_Page */
    /** @var AM_Model_Db_Field */
    protected $_oField      = null; /**< @type AM_Model_Db_Field */
    /** @var string */
    protected $_sFieldTypeTitle = null; /**< @type string */

    const RESOURCE_TYPE         = 'element';
    const RESOURCE_CLASS_PREFIX = 'AM_Model_Db_Element_Data';

    /**
     * Get data from current object, gives it a new page and insert new record
     * @param AM_Model_Db_Page $oPage
     * @return AM_Model_Db_Element
     */
    public function copyToPage(AM_Model_Db_Page $oPage)
    {
        $oResources   = $this->getResources();

        $aData            = array();
        $aData['page']    = $oPage->id;
        $aData['updated'] = null;
        $this->copy($aData);
        $oResources->copy();

        return $this;
    }

    /**
     * Set element resource object
     * @param AM_Model_Db_Element_Data_Abstract $oResources
     * @return AM_Model_Db_Element
     */
    public function setResources(AM_Model_Db_Element_Data_Abstract $oResources)
    {
        $this->_oResources = $oResources;

        return $this;
    }

    /**
     * Get element resource object
     * @return AM_Model_Db_Element_Data_Abstract
     */
    public function getResources()
    {
        if (empty($this->_oResources)) {
            $this->fetchResources();
        }

        return $this->_oResources;
    }

    /**
     * Fetch element resource object
     * @return AM_Model_Db_Element
     */
    public function fetchResources()
    {
        $sPostfix            = Zend_Filter::filterStatic($this->getFieldTypeTitle(), 'Word_UnderscoreToCamelCase');
        $sResourcesClassName = self::RESOURCE_CLASS_PREFIX . '_' . $sPostfix;

        if (!class_exists($sResourcesClassName, true)) {
            throw new AM_Model_Db_Exception(sprintf('Element data class "%s" not found', $sResourcesClassName));
        }

        $this->_oResources = new $sResourcesClassName($this);

        return $this;
    }

    /**
     * Get element's page
     * @return AM_Model_Db_Page
     */
    public function getPage()
    {
        if (empty($this->_oPage)) {
            $this->fetchPage();
        }

        return $this->_oPage;
    }

    /**
     * Fetch element's page
     * @return AM_Model_Db_Element
     */
    public function fetchPage()
    {
        $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => $this->page));

        if (!empty($oPage)){
            $this->setPage($oPage);
        }

        return $this;
    }

    /**
     * Set element's page
     * @param AM_Model_Db_Page $oPage
     * @return AM_Model_Db_Element
     */
    public function setPage(AM_Model_Db_Page $oPage)
    {
        $this->_oPage = $oPage;

        return $this;
    }

    /**
     * Get element's field
     * @return AM_Model_Db_Field
     */
    public function getField()
    {
        if (empty($this->_oField)) {
            $this->fetchField();
        }

        return $this->_oField;
    }

    /**
     * Fetch element's field
     * @return AM_Model_Db_Element
     */
    public function fetchField()
    {
        $this->_oField = AM_Model_Db_Table_Abstract::factory('field')->findOneBy(array('id' => $this->field));

        if (is_null($this->_oField)) {
            throw new AM_Model_Db_Exception(sprintf('Element "%s" has no field', $this->id));
        }

        return $this;
    }

    /**
     * Get element's field type title
     *
     * @return string Field type title
     */
    public function getFieldTypeTitle()
    {
        if (is_null($this->_sFieldTypeTitle)) {
            $this->_sFieldTypeTitle = $this->getField()->getFieldType()->title;
        }

        return $this->_sFieldTypeTitle;
    }

    /**
     * Upload resource
     *
     * @param string $sResourceKey resource name
     * @return AM_Model_Db_Element
     */
    public function uploadResource($sResourceKey = AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE)
    {
        $oResources = $this->getResources();

        if (method_exists($oResources, 'upload')) {
            /* @var $oResources AM_Model_Db_Element_Data_Resource */
            $oResources->upload($sResourceKey);
        }

        return $this;
    }

    /**
     * Allows post-delete logic to be applied to row.
     *
     * @return void
     */
    protected function _postDelete()
    {
        $this->getResources()->delete();
    }

    /**
     * Delete element data by key
     *
     * @param string $sKey
     * @return AM_Model_Db_Element
     * @throws AM_Model_Db_Exception
     */
    public function deleteDataByKey($sKey)
    {
        $sKey = (string) $sKey;

        if (empty($sKey)) {
            throw new AM_Model_Db_Exception('Trying to delete data with empty key');
        }

        $this->getResources()->delete($sKey);

        $this->updated = new Zend_Db_Expr('NOW()');
        $this->save();

        return $this;
    }
}
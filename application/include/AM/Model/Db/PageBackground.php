<?php
/**
 * @file
 * AM_Model_Db_PageBackground class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
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
<?php
/**
 * @file
 * AM_View_Helper_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_View_Helper
 */

/**
 * @ingroup AM_View_Helper
 */
abstract class AM_View_Helper_Abstract extends Zend_View_Helper_Abstract
{
    /** @var string **/
    protected $_sName = null; /**< @type string */

    /** @var AM_Controller_Action_Helper_Smarty */
    public $oView = null; /**< @type AM_Controller_Action_Helper_Smarty */

    /**
     * Creates data for view
     */
    abstract public function show();

    /**
     * Get helper name
     * @return string
     */
    public function getName()
    {
        if (is_null($this->_sName)) {
            $sClassName   = get_class($this);
            $aChunks      = explode('_', $sClassName);
            $this->_sName = array_pop($aChunks);

            $oFilter = new Zend_Filter();
            $oFilter->addFilter(new Zend_Filter_Word_CamelCaseToUnderscore())
                    ->addFilter(new Zend_Filter_StringToLower());
            $this->_sName = $oFilter->filter($this->_sName);
        }

        return $this->_sName;
    }

    /**
     * Get action helper
     * @param string $sName
     * @return Zend_View_Helper_Abstract
     */
    public function getHelper($sName)
    {
        return $this->oView->getActionController()->getHelper($sName);
    }

    /**
     * @todo: we have to use normal Zend_View_Interface
     * Set the View object
     *
     * @param  AM_Controller_Action_Helper_Smarty $oView
     * @return Zend_View_Helper_Abstract
     */
    public function setViewHelper($oView)
    {
        $this->oView = $oView;

        return $this;
    }

    /**
     * Returns view helper
     *
     * @return AM_Controller_Action_Helper_Smarty
     * @throws AM_View_Helper_Exception
     */
    public function getView()
    {
        if (is_null($this->oView)) {
            throw new AM_View_Helper_Exception('View object not found');
        }

        return $this->oView;
    }
}

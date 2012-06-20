<?php
/**
 * @file
 * AM_View_Helper_Abstract class definition.
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

<?php
/**
 * @file
 * AM_Controller_Action_Helper_Localizer class definition.
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
 * This class is responsible for localization
 *
 * @ingroup AM_Controller_Action_Helper
 * @ingroup AM_Controller_Action
 */
class AM_Controller_Action_Helper_Localizer extends Zend_Controller_Action_Helper_Abstract
{

    /** @var string Name of translation adapter */
    protected $_sTranslationAdapterName; /**< @type string */
    /** @var List of path to translations files */
    protected $_aTranslationsPaths; /**< @type array */
    /** @var string Name of param for choosing locale */
    protected $_sRequestParamName; /**< @type string */
    /** @var boolean Parse request headers for autodetecting locale */
    protected $_bParseRequestHeaders; /**< @type boolean */
    /** @var string Name of cookie for retrieveng/storing locale */
    protected $_sCookieName; /**< @type string */

    /**
     * Constructor
     *
     * @param string $sTranslationAdapter Name of translation adapter
     * @param array $aTranslationsPaths List of path to translations files
     * @param string $sRequestParamName Name of param for choosing locale
     * @param bool $bParseRequestHeaders Parse request headers for autodetecting locale
     * @param string $sCookieName Name of cookie for retrieveng/storing locale
     */
    public function __construct($sTranslationAdapter, array $aTranslationsPaths, $sRequestParamName = 'locale', $bParseRequestHeaders = true, $sCookieName = null)
    {
        $this->_sTranslationAdapterName = $sTranslationAdapter;
        $this->_aTranslationsPaths      = $aTranslationsPaths;
        $this->_sRequestParamName       = $sRequestParamName;
        $this->_sCookieName             = $sCookieName;
        $this->_bParseRequestHeaders    = $bParseRequestHeaders;
    }

    /**
     * setActionController()
     *
     * @param Zend_Controller_Action $oActionController
     * @return Zend_Controller_ActionHelper_Abstract
     */
    public function setActionController(Zend_Controller_Action $oActionController = null)
    {
        static $bSmartyInitialized;

        foreach ($this->_aTranslationsPaths as $sName => $sPath) {
            $this->_aTranslationsPaths[$sName] = Volcano_Tools::fixPath($sPath);
        }

        $aProposedLocales = array();
        if ($this->_sRequestParamName && $sParam = $oActionController->getRequest()->getParam($this->_sRequestParamName)) {
            $aProposedLocales[] = $sParam;
        }
        $sCookieLocale = false;
        if ($this->_sCookieName && $sCookieLocale = $oActionController->getRequest()->getCookie($this->_sCookieName)) {
            $aProposedLocales[] = $sCookieLocale;
        }

        $oLocalizer = new Volcano_Localizer($aProposedLocales, $this->_sTranslationAdapterName, $this->_aTranslationsPaths, $this->_bParseRequestHeaders);
        if ($sCookieLocale != $oLocalizer->getLocale()->getLanguage()) {
            setcookie($this->_sCookieName,$oLocalizer->getLocale()->getLanguage(), time() + 60 * 60 * 24 * 30, "/");
        }

        parent::setActionController($oActionController);
        $oActionController->localizer = $oLocalizer;

        //add localizer to smarty
        if (Zend_Controller_Action_HelperBroker::hasHelper('Smarty') && !$bSmartyInitialized) {
            $oSmartyHelper = Zend_Controller_Action_HelperBroker::getExistingHelper('Smarty');
            $oSmartyHelper->addCustomFunction('modifier', array($oLocalizer, 'translate'), 'translate');
            $oSmartyHelper->locale = array(
                'language' => $oLocalizer->getLocale()->getLanguage(),
                'region'   => $oLocalizer->getLocale()->getRegion()
            );
            $bSmartyInitialized = TRUE;
        }

        return $this;
    }
}
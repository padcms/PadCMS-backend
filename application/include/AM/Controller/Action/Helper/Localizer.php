<?php
/**
 * @file
 * AM_Controller_Action_Helper_Localizer class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
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
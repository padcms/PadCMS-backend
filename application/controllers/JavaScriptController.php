<?php
/**
 * @file
 * JavaScriptController class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Controller_Action
 */
class JavaScriptController extends AM_Controller_Action
{
    const LOCALE_EXPIRES_DAYS = 20;
    const CHECK_AUTH_EXPIRES_MINUTES = 2;

    public function indexAction()
    {
        exit;
    }

    /**
     * JavaScript get-locale action
     */
    public function getLocaleAction()
    {
        $aLocale = array();

        try {
            $sFileName = APPLICATION_PATH.'/i18n/'.$this->localizer->getLocale()->getLanguage().'.txt';

            if (!file_exists($sFileName) || !is_file($sFileName) || !is_readable($sFileName)) {
                throw new AM_Component_Exception('Localisation file not found');
            }

            $aFile = file($sFileName);

            while(list($iLineNumber, $sLine) = each($aFile)) {
                $sLine = trim($sLine);

                if (!$sLine || strlen($sLine) < 3 || substr($sLine, 0, 3) != 'js_') {
                    continue;
                }

                $aPair = explode('=', substr($sLine, 3));
                if (count($aPair) != 2 || !$aPair[0] || !$aPair[1]) {
                    continue;
                }

                $aLocale[$aPair[0]] = $aPair[1];
            }


            $oDate = new Zend_Date();
            $oDate->addDay(self::LOCALE_EXPIRES_DAYS);

            $this->getResponse()->setHeader('Content-type', 'text/javascript')
                    ->setHeader('Content-Disposition:inline', ' filename=locale.js')
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Cache-Control', 'maxage='.(60 * 60 * 24 * self::LOCALE_EXPIRES_DAYS), true)
                    ->setHeader('Expires', $oDate->toString('EE, d M Y H:i:s ').'GMT', true);

        } catch (Exception $ex) {

        }

        $this->view->locale = $aLocale;
    }

    /**
     * JavaScript check-auth-ajax action
     */
    public function checkAuthAjaxAction()
    {
        $bIsLoggedin = false;
        if ($this->getUser()) {
            $bIsLoggedin = true;
        }

        return $this->getHelper('Json')->sendJson(array('result' => $bIsLoggedin));
    }

    /**
     * JavaScript reload-parent action
     */
    public function reloadParentAction()
    {

    }

    /**
     * JavaScript iframe-dialog-close action
     */
    public function iframeDialogCloseAction()
    {

    }
}
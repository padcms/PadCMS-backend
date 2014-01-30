<?php
/**
 * @file
 * JavaScriptController class definition.
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

    /**
     * Log js error.
     */
    public function logErrorAction() {
        $sError = $this->_getParam('error');
        $this->getLogger()->crit(sprintf('URI: %s JS_EXCEPTION_OBJECT: %s', $this->getRequest()->getRequestUri(), $sError), array('file' => 'ErrorController'));
        $this->getHelper('Json')->sendJson(array('result' => 1));
    }
}
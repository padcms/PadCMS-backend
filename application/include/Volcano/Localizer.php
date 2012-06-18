<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Localizer
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Zend/Exception.php';
include_once 'Zend/Translate.php';
include_once 'Zend/Session/Namespace.php';
/**
 * Localization and internatiolizanion
 *
 * @category Volcano
 * @package Volcano_Component
 */
class Volcano_Localizer {


    /**
     * Paths to locales files
     * in array: LocaleName => PathToFile
     * @var array
     */
    protected $translationsPaths = array();

    /**
     * Name of class translation adapter
     * @var string
     */
    protected $translationAdapter = "";


    /**
     * Name of current locale
     * @var string
     */
    protected $localeName = "";

    /**
     * Locale object
     * @var Zend_Locale
     */
    protected $locale;

    /**
     * Tramslation object
     * @var Zend_Translate
     */
    protected $translate;

    /**
     * Session namespace
     * @var Zend_Session_Namespace
     */
    protected $session;

    /**
     * Constructor
     * @param string/array $recommendedLocales Name of reccomended locale(s)
     * @param string $translationAdapter Name of translation adapter
     * @param array $translationsPaths List of path to translations files
     * @param bool $parseRequestHeaders Parse request headers for autodetecting locale
     */
    public function __construct($recommendedLocales, $translationAdapter, array $translationsPaths, $parseRequestHeaders = true) {
        $this->translationsPaths = $translationsPaths;
        $this->translationAdapter = $translationAdapter;
        $this->session = new Zend_Session_Namespace("Localizer");

        $oldSessionLocale = $this->session->localeName;

        //try set recommended locale
        $success = false;
        if ($recommendedLocales) {
            if (!is_array($recommendedLocales)) {
                $recommendedLocales = array($recommendedLocales);
            }
            foreach ($recommendedLocales as $recommendedLocale) {
                try {
                    $this->setLocale($recommendedLocale);
                    $success = true;
                } catch (Zend_Exception $e) {
                }
                if ($success) {
                    break;
                }
            }
        } else {
            $success = false;
        }
        //try set locale stored in session
        if (!$success && $oldSessionLocale) {
            try {
                $this->setLocale($oldSessionLocale);
                $success = true;
            } catch (Zend_Exception $e) {
            }
        }
        //try set automatic (from browser)
        if (!$success && $parseRequestHeaders ) {
            try {
                $this->setLocale();
                $success = true;
            } catch (Zend_Exception $e) {
            }

        }
        //try set default
        if (!$success) {
            foreach ($this->translationsPaths as $name => $value) {
                try {
                    $this->setLocale($name);
                    $success = true;
                    break;
                } catch (Zend_Exception $e) {
                }
            }
        }
        //throw exception if cannot set locale
        if (!$success) {
            include_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("Localizer: Cannot set locale");
        }
    }

    /**
     * Try to set given locale
     * Throw Zend_Translate_Exception if cannot
     */
    public function setLocale($name = null) {
        try {
            if (!$this->localeName) {
                $this->locale = new Zend_Locale($name);
            } else {
                $this->locale->setLocale($name);
            }
            if (array_key_exists($this->locale->getLanguage(), $this->translationsPaths)) {
                if (!isset($this->translate)) {
                    $this->translate = new Zend_Translate($this->translationAdapter, $this->translationsPaths[$this->locale->getLanguage()], $this->locale->getLanguage());
                } elseif ($this->translate->isAvailable($this->locale->getLanguage())) {
                    $this->translate->setLocale($this->locale->getLanguage());
                } else {
                    $this->translate->addTranslation($this->translationsPaths[$this->locale->getLanguage()], $this->locale->getLanguage());
                }
            } else {
                include_once 'Zend/Translate/Exception.php';
                throw new Zend_Translate_Exception("Unsupported language");
            }
        } catch(Zend_Exception $e) {
            if ($this->localeName) {
                $this->setLocale($this->localeName);
            }
            throw $e;
        }
        $this->localeName = $this->session->localeName = $this->locale->getLanguage() . ($this->locale->getRegion() ? "_" . $this->locale->getRegion() : "");
        Zend_Registry::set('Zend_Locale', $this->locale);
        Zend_Locale::setDefault($this->locale);
    }

    /**
     * Return current locale object
     * @return Zend_Locale
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Retturn translation string
     * example $l->translate('Field %1 is incorrect', 'FieldName');
     *
     * @param string $msg Message to transalte.
     */
    public function translate($msg) {
        $translated = $msg;
        if ($this->translate->isTranslated($msg, true, $this->locale)) {
            $translated = $this->translate->translate($msg);
        } else {
            foreach ($this->translationsPaths as $name => $value) {
                if (!$this->translate->isAvailable($name)) {
                    try {
                        $this->translate->addTranslation($this->translationsPaths[$name], $name);
                        $this->translate->setLocale($this->getLocale());
                    } catch (Zend_Translate_Exception $e) {
                        continue;
                    }
                }
                if ($this->translate->isTranslated($msg, $name)) {
                    $translated = $this->translate->translate($msg, $name);
                    break;
                }
            }
        }
        if (func_num_args() > 1) {
            $params = func_get_args();
            $params[0] = $translated;
            $translated = @call_user_func_array("sprintf", $params); //add shield for incorrect translations(warning about incorrect number of arguments)
        }
        return $translated;
    }

    /**
     * Return array with all loaded translations
     * for current language
     * @param string $locale Locale name, if not set used current locale
     * @return array Translations
     */
    public function getTranslations($locale = null) {
        return $this->translate->getAdapter()->getMessages($locale);
    }

}
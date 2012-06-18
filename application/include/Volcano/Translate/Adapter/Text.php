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

include_once 'Zend/Translate/Adapter.php';
/**
 * Translations adapter
 *
 * based on Zend_Transle_Adapter_Gettext
 *
 * @category Volcano
 * @package Volcano_Component
 */
class Volcano_Translate_Adapter_Text extends Zend_Translate_Adapter {

    private $_data = array();

    /**
     * Load translation data
     *
     * @param  string  $filename  file to add, full path must be given for access
     * @param  string  $locale    New Locale/Language to set, identical with locale identifier,
     *                            see Zend_Locale for more information
     * @param  array   $option    OPTIONAL Options to use
     * @throws Zend_Translation_Exception
     */
    protected function _loadTranslationData($filename, $locale, array $options = array()) {
        $options = array_merge($this->_options, $options);

        if (!file_exists($filename)) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("Text file '".$filename."' not found");
        }

        $strings = @file($filename);

        foreach ($strings as $string) {
            $parts = explode("=", $string, 2);
            if (count($parts) == 2) {
                $this->_data[$locale][trim($parts[0])] = trim($parts[1]);
            }
        }
        return $this->_data;
    }


    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString() {
        return "Text";
    }

}

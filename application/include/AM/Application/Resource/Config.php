<?php
/**
 * @file
 * AM_Application_Resource_Config class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the old config
 * @ingroup AM_Application
 */
class AM_Application_Resource_Config extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return AM_Log
     */
    public function init()
    {
        $aOptions = $this->getOptions();

        $sConfigPath = APPLICATION_PATH . DIRECTORY_SEPARATOR .'configs' . DIRECTORY_SEPARATOR .'config.ini';
        if (array_key_exists('path', $aOptions)) {
            $sConfigPath = $aOptions['path'];
        }

        $oConfig = new Zend_Config_Ini($sConfigPath);

        return $oConfig;
    }
}

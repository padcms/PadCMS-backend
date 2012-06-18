<?php
/**
 * @file
 * AM_Application_Resource_Handlermanager class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the handlers
 * @ingroup AM_Application
 * @ingroup AM_Handler
 */
class AM_Application_Resource_Handlermanager extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return AM_Handler_Locator
     */
    public function init()
    {
        $oHandlerLocator = AM_Handler_Locator::getInstance();
        $aOptions = $this->getOptions();

        foreach ($aOptions as $sHandlerName => $sHandlerClass) {
            $oHandlerLocator->setHandler($sHandlerName, $sHandlerClass);
        }

        return $oHandlerLocator;
    }
}

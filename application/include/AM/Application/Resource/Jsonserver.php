<?php
/**
 * @file
 * AM_Application_Resource_Jsonserver class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the Json-Rpc server
 * @ingroup AM_Application
 */
class AM_Application_Resource_Jsonserver extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Json_Server
     */
    public function init()
    {
        $aOptions = $this->getOptions();

        $oJsonServer = new Zend_Json_Server();

        foreach ($aOptions['classes'] as $sClassName => $sAlias) {
            $oJsonServer->setClass($sClassName, $sAlias);
        }

        return $oJsonServer;
    }
}

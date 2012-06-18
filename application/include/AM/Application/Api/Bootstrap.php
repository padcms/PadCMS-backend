<?php
/**
 * @file
 * AM_Application_Api_Bootstrap class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Application
 */
class AM_Application_Api_Bootstrap extends AM_Application_Bootstrap
{
    /**
     * Run the JSON-RPC server
     *
     * @return void
     */
    public function run()
    {
        $this->bootstrap('jsonserver');

        $oJsonServer = $this->getResource('jsonserver');
        /* @var $oJsonServer Zend_Json_Server */

        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            // Indicate the URL endpoint, and the JSON-RPC version used:
            $oJsonServer->setTarget('/api/v1/jsonrpc.php')
                    ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

            // Grab the SMD
            $oServiceMap = $oJsonServer->getServiceMap();

            // Return the SMD to the client
            header('Content-Type: application/json');
            echo $oServiceMap;
            return;
        } else {
            $this->getResource('log')->debug(file_get_contents('php://input'), array('file' => 'Zend_Json_Server'));
        }

        header('Content-Type: application/json');
        $oJsonServer->handle();
    }
}
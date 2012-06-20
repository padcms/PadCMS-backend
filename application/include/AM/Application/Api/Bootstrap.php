<?php
/**
 * @file
 * AM_Application_Api_Bootstrap class definition.
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
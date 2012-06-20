<?php
/**
 * @file
 * AM_Log class definition.
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
 * @defgroup AM_Log
 */

/**
 * The base logger class
 * @ingroup AM_Log
 */
class AM_Log extends Zend_Log
{
    /**
     * Factory to construct the logger and one or more writers
     * based on the configuration array
     *
     * @param  array|Zend_Config Array or instance of Zend_Config
     * @return AM_Log
     * @throws Zend_Log_Exception
     */
    static public function factory($aConfig = array())
    {
        if ($aConfig instanceof Zend_Config) {
            $aConfig = $aConfig->toArray();
        }

        if (!is_array($aConfig) || empty($aConfig)) {
            /** @see Zend_Log_Exception */
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
        }

        $oLog = new self;

        if (array_key_exists('timestampFormat', $aConfig)) {
            if (null != $aConfig['timestampFormat'] && '' != $aConfig['timestampFormat']) {
                $oLog->setTimestampFormat($aConfig['timestampFormat']);
            }
            unset($aConfig['timestampFormat']);
        }

        if (!is_array(current($aConfig))) {
            $oLog->addWriter(current($aConfig));
        } else {
            foreach($aConfig as $writer) {
                $oLog->addWriter($writer);
            }
        }

        return $oLog;
    }

    /**
     * Register Logging system as an error handler to log php errors
     * Register AM_Log::errorHandlerFatal method as shutdown function
     * Note: it still calls the original error handler if set_error_handler is able to return it.
     *
     * Errors will be mapped as:
     *   E_NOTICE, E_USER_NOTICE => NOTICE
     *   E_WARNING, E_CORE_WARNING, E_USER_WARNING => WARN
     *   E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_RECOVERABLE_ERROR => ERR
     *   E_DEPRECATED, E_STRICT, E_USER_DEPRECATED => DEBUG
     *   (unknown/other) => INFO
     *
     * @link http://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     * @link http://php.net/manual/en/function.register-tick-function.php
     * @see Zend_Log::registerErrorHandler()
     *
     * @return AM_Log
     */
    public function registerErrorHandler()
    {
        parent::registerErrorHandler();

        register_shutdown_function(array($this, 'errorHandlerFatal'));

        return $this;
    }

    /**
     * Error handler for fatal errors
     *
     * @return void
     */
    public function errorHandlerFatal() {
        $lastError = error_get_last();

        if ($lastError['type'] === E_ERROR) {
            $this->errorHandler(E_ERROR, $lastError['message'], $lastError['file'], $lastError['line'], null);
        }
    }
}
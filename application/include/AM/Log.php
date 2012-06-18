<?php
/**
 * @file
 * AM_Log class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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
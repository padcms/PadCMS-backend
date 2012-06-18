<?php
/**
 * @file
 * AM_Application_Resource_Log class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the log
 * @ingroup AM_Application
 * @ingroup AM_Log
 */
class AM_Application_Resource_Log extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var AM_Log
     */
    protected $_oLog;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return AM_Log
     */
    public function init()
    {
        return $this->getLog();
    }

    /**
     * Attach logger
     *
     * @param  AM_Log $oLog
     * @return AM_Application_Resource_Log
     */
    public function setLog(AM_Log $oLog)
    {
        $this->_oLog = $oLog;

        return $this;
    }

    /**
     * Retruns logger instance
     *
     * @return AM_Log
     */
    public function getLog()
    {
        if (null === $this->_oLog) {
            $aOptions = $this->getOptions();

            $oLog = AM_Log::factory($aOptions);

            $oLog->registerErrorHandler();

            $this->setLog($oLog);
        }

        return $this->_oLog;
    }
}

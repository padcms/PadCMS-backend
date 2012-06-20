<?php
/**
 * @file
 * AM_Handler_Abstract class definition.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Handler
 */

/**
 * Abstract class: this is the superclass for all Handler classes
 *
 * This class is responsible for the getting and setting logger and system configuration
 *
 * @ingroup AM_Handler
 */
abstract class AM_Handler_Abstract
{
    /** @var Zend_Log */
    protected $_oLogger = null; /**< @type Zend_Log */
    /** @var Zend_Config */
    protected $_oConfig = null; /**< @type Zend_Config */

    /**
     * Get the logger instance
     *
     * @return Zend_Log
     */
    public function getLogger()
    {
        if (is_null($this->_oLogger)) {
            $this->_oLogger = Zend_Registry::get('log');
        }
        $this->_oLogger->setEventItem('file', get_class($this));

        return $this->_oLogger;
    }

    /**
     * Set the logger instance
     *
     * @param Zend_Log $oLogger
     * @return AM_Cli_Task_Abstract
     */
    public function setLogger(Zend_Log $oLogger)
    {
        $this->_oLogger = $oLogger;

        return $this;
    }

    /**
     * Get the config instance
     *
     * @return Zend_Config
     */
    public function getConfig()
    {
        if (is_null($this->_oConfig)) {
            $this->_oConfig = Zend_Registry::get('config');
        }

        return $this->_oConfig;
    }

    /**
     * Set the config instance
     *
     * @param Zend_Config $oConfig
     * @return AM_Handler_Abstract
     * @throws AM_Handler_Exception
     */
    public function setConfig(Zend_Config $oConfig)
    {
        $this->_oConfig = $oConfig;

        return $this;
    }
}
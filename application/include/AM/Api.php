<?php
/**
 * @file
 * AM_Api class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Api
 */

/**
 * Abstract class: this is the superclass for all API classes
 * @ingroup AM_Api
 */
abstract class AM_Api
{
    const PLATFORM_IOS     = 'ios';
    const PLATFORM_ANDROID = 'android';

    /** @var array */
    protected $_aValidPlatforms = array(self::PLATFORM_IOS, self::PLATFORM_ANDROID); /**< @type array */

    /** @var Zend_Config */
    protected $_oConfig = null; /**< @type Zend_Config */

    /** @var Zend_Db_Adapter_Abstract */
    protected $_oDbAdapter = null; /**< @type Zend_Db_Adapter_Abstract */

    /** @var Zend_Log */
    protected $_oLogger = null; /**< @type Zend_Log */

    public function __construct()
    {
        $this->_oConfig    = Zend_Registry::get('config');
        $this->_oLogger    = Zend_Registry::get('log');
        $this->_oDbAdapter = Zend_Registry::get('db');
    }

    /**
     * Get the logger instance
     *
     * @return Zend_Log
     */
    public function getLogger()
    {
        if (is_null($this->_oLogger)) {
            $this->_oLogger = Zend_Registry::get('logger');
            $this->_oLogger->setEventItem('file', get_class($this));
        }

        return $this->_oLogger;
    }
}
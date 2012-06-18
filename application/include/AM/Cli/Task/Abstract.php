<?php
/**
 * @file
 * AM_Cli_Task_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Abstract class: this is the superclass for all Cli tasks
 * @ingroup AM_Cli
 */
abstract class AM_Cli_Task_Abstract
{
    /** @var array The colors of console message*/
    protected static $_aColors = array('info' => 9, 'success' => 2, 'error' => 1); /**< @type array */
    /** @var array The list of arguments*/
    protected $_aExpectedArguments = array(); /**< @type array */
    /** @var array The list of options*/
    protected $_aExpectedOptions   = array(); /**< @type array */
    /** @var Zend_Config **/
    protected $_oConfig = null; /**< @type Zend_Config */
    /** @var Zend_Log **/
    protected $_oLogger = null; /**< @type Zend_Log */
    /** @var Zend_Console_Getopt */
    protected $_oGetopt = null; /**< @type Zend_Console_Getopt */

    public function __construct()
    {
        $this->_configureDefault()
             ->_configure();

        $this->_parseParams();
    }

    /**
     * Task configuration
     */
    abstract protected function _configure();

    /**
     * Get logger
     *
     * @return Zend_Log
     */
    public function getLogger()
    {
        if (is_null($this->_oLogger)) {
            $this->_oLogger = Zend_Registry::get('log');
        }

        return $this->_oLogger;
    }

    /**
     * Set logger
     *
     * @param Zend_Log $logger
     * @return AM_Cli_Task_Abstract
     */
    public function setLogger(Zend_Log $logger)
    {
        $this->_oLogger = $logger;

        return $this;
    }

    /**
     * Get config
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
     * Set default task params
     *
     * @return AM_Cli_Task_Abstract
     */
    protected function _configureDefault()
    {
        $this->addOption('env', 'e', '=s', 'Set environment', 'development');
        $this->addOption('log-console', 'l', '-i', 'Write log STDOUT');

        return $this;
    }

    /**
     * Add argument to task
     *
     * @param string $sName
     * @param string $sType
     * @param string $sDefault
     * @return void
     */
    public final function addArgument($sName, $sType, $sDefault = null)
    {
        $this->_aExpectedArguments[$sName] = array('type' => $sType, 'value' => $sDefault);
    }

    /**
     * Add option to task
     *
     * @param string $sName
     * @param string $sAlias
     * @param string $sType
     * @param string $sDefault
     * @return void
     */
    public final function addOption($sName, $sAlias, $sType, $sDescription, $sDefault = null)
    {
        $this->_aExpectedOptions[$sName] = array('alias' => $sAlias, 'type' => $sType, 'description' => $sDescription, 'default' => $sDefault);
    }

    /**
     * Running task
     *
     * @return void
     */
    public final function run()
    {
        if ($this->_getOption('log-console')) {
            $oLogWriter    = new Zend_Log_Writer_Stream('php://output');
            $oLogFormatter = new Zend_Log_Formatter_Simple('%priorityName%: [%file%:%line%] %message%' . PHP_EOL);
            $oLogWriter->setFormatter($oLogFormatter);

            $this->getLogger()->addWriter($oLogWriter);
        }

        $this->getLogger()->setEventItem('file', get_class($this));
        $this->getLogger()->setEventItem('line', null);
        $this->getLogger()->info('Running');

        try {
            $this->execute();
        } catch (Exception $oException) {
           $this->getLogger()->crit($oException);
        }
    }

    /**
     * Executions logic
     */
    abstract public function execute();

    /**
     * Prepare options array according to Zend rules
     *
     * @return array
     */
    protected final function _parseOptions()
    {
        $aOptionsParsed = array();

        foreach ($this->_aExpectedOptions as $sKey => $aOption) {
            $sKey = $sKey . '|' . $aOption['alias'] . $aOption['type'];
            $aOptionsParsed[$sKey] = $aOption['description'];
        }

        return $aOptionsParsed;
    }

    /**
     * Parsing arguments
     *
     * @return array
     */
    protected final function _parseArguments()
    {
        $mArguments = $_SERVER['argv'];
        $mArguments = implode(' ', $mArguments);
        $mArguments = preg_replace('/(\'|")(.+?)\\1/e', "str_replace(' ', '=PLACEHOLDER=', '\\2')", $mArguments);
        $mArguments = preg_split('/\s+/', $mArguments);
        $mArguments = str_replace('=PLACEHOLDER=', ' ', $mArguments);

        $sTaskName = array_shift($mArguments);

        $aArguments = array();
        foreach ($mArguments as $sArgument) {
            if (0 !== strpos($sArgument, '--')) {
                  $aArguments[] = $sArgument;
            }
        }

        foreach ($this->_aExpectedArguments as $sKey => $aOptions) {
            $sArgument = array_shift($aArguments);
            $this->_aExpectedArguments[$sKey]['value'] = $sArgument;
        }
    }

    /**
     * Prepare options & aguments
     *
     * @return void
     */
    protected final function _parseParams()
    {
        $this->_parseArguments();
        $aOptions = $this->_parseOptions();
        try {
            $this->_oGetopt = new Zend_Console_Getopt($aOptions);
            $this->_oGetopt->parse();
        } catch (Zend_Console_Getopt_Exception $oException){
            echo $oException->getUsageMessage();
            exit;
        }
    }

    /**
     * Gets argument value by name
     *
     * @return string
     */
    protected final function _getArgument($sName)
    {
        if (!isset($this->_aExpectedArguments[$sName])) {
            return false;
        }

        return $this->_aExpectedArguments[$sName]['value'];
    }

    /**
     * Get option value by name
     *
     * @param string $sName
     * @return string
     */
    protected final function _getOption($sName)
    {
        $sOption = $this->_oGetopt->getOption($sName);

        if (is_null($sOption) && !is_null($this->_aExpectedOptions[$sName]['default']))
        {
            $sOption = $this->_aExpectedOptions[$sName]['default'];
        }

        return $sOption;
    }

    /**
     * Print formatted string to stdout
     *
     * @param string $sMessage
     * @param string $sStatus
     */
    protected final function _echo($sMessage, $sStatus = 'info')
    {
        if (!array_key_exists($sStatus, self::$_aColors)) {
            $iColor = self::$_aColors['info'];
        } else {
            $iColor = self::$_aColors[$sStatus];
        }

        passthru("tput setaf {$iColor}");
        echo $sMessage;
        switch ($sStatus) {
            case 'success':
                passthru('echo -n "$(tput hpa $(tput cols))$(tput cub 6)[OK]"');
            break;
            case 'error':
                passthru('echo -n "$(tput hpa $(tput cols))$(tput cub 6)[ERR]"');
            break;
        }
        echo PHP_EOL;
        passthru("tput sgr0");
    }
}

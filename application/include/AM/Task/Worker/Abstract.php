<?php
/**
 * @file
 * AM_Task_Worker_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This is the superclass for all task classes
 * @ingroup AM_Task
 */
abstract class AM_Task_Worker_Abstract implements AM_Task_Worker_Interface
{
    const STATUS_NEW    = 'new';
    const STATUS_RUN    = 'run';
    const STATUS_FINISH = 'finish';
    const STATUS_ERROR  = 'error';

    /** @var array */
    protected $_aOptions      = array(); /**< @type array */
    /** @var AM_Model_Db_TaskType */
    protected $_oTaskType     = null; /**< @type AM_Model_Db_TaskType */
    /** @var AM_Model_Db_Task */
    protected $_oTask = null; /**< @type AM_Model_Db_Task */
    /** @var AM_Log **/
    private $_oLogger = null; /**< @type AM_Log */
    /** @car Zend_Config **/
    private $_oConfig = null; /**< @type Zend_Config */

    /**
     * @param array | null $options
     */
    public final function __construct($aOptions = null)
    {
        if (!is_null($aOptions)) {
            $this->setOptions($aOptions);
        }
    }

    /**
     * Get logger
     * @return AM_Log
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
     * Set logger
     * @param AM_Log $oLogger
     * @return AM_Task_Worker_Abstract
     */
    public function setLogger(AM_Log $oLogger)
    {
        $this->_oLogger = $oLogger;

        return $this;
    }

    /**
     * Get configuration
     * @return Zend_Config
     */
    public function getConfig()
    {
        if (is_null($this->_oConfig)) {
            $this->_oConfig = Zend_Registry::get("config");
        }

        return $this->_oConfig;
    }

    /**
     * Set task options
     * @param array $aOptions
     * @return AM_Task_Worker_Abstract
     */
    public function setOptions($aOptions)
    {
        $this->_aOptions = (array) $aOptions;

        return $this;
    }

    /**
     * Return task options
     * @return array
     */
    public function getOptions()
    {
        return $this->_aOptions;
    }

    /**
     * Get value of option
     * @param string $sKey
     * @return mixed
     * @throws AM_Task_Worker_Abstract
     */
    public function getOption($sKey)
    {
        if (!array_key_exists($sKey, $this->_aOptions)) {
            throw new AM_Task_Worker_Exception(sprintf('Option \'%s\' not found', $sKey));
        }

        return $this->_aOptions[$sKey];
    }

    /**
     * Add option to worker options
     * @param string $sKey
     * @param mixed $mValue
     * @return AM_Task_Worker_Abstract
     */
    public function addOption($sKey, $mValue)
    {
        $this->_aOptions[$sKey] = $mValue;

        return $this;
    }


    /**
     * Get current task type
     * @return AM_Model_Db_TaskType
     */
    protected function _getTaskType()
    {
        if (is_null($this->_oTaskType)) {
            $oTaskType = AM_Model_Db_Table_Abstract::factory('task_type')
                ->findOneBy(array('class' => get_class($this)));

            if (empty($oTaskType)) {
                throw new AM_Task_Worker_Exception('Task type is undefined');
            }

            $this->_oTaskType = $oTaskType;
        }

        return $this->_oTaskType;
    }

    /**
     * Return task model instance
     * @return AM_Model_Db_Task
     */
    protected function _getTask()
    {
        if (is_null($this->_oTask)) {
            $this->_oTask = new AM_Model_Db_Task();
        }

        return $this->_oTask;
    }

    /**
     * Set tasks active record
     * @param AM_Model_Db_Task $oTask
     * @return AM_Task_Worker_Abstract
     */
    public function setTask(AM_Model_Db_Task $oTask)
    {
        $this->_oTask = $oTask;

        return $this;
    }

    /**
     * Set task type active record
     * @param AM_Model_Db_TaskType $oTaskType
     * @return AM_Task_Worker_Abstract
     */
    public function setTaskType(AM_Model_Db_TaskType $oTaskType)
    {
        $this->_oTaskType = $oTaskType;

        return $this;
    }

    /**
     * Finish task
     * @return AM_Task_Worker_Abstract
     * @throws AM_Task_Worker_Exception
     */
    public final function finish()
    {
        $oTask = $this->_getTask();

        if (is_null($oTask->id)) {
            throw new AM_Task_Worker_Exception('Trying to finish non created task');
        }

        $this->_doFinish();

        $oTask->status  = self::STATUS_FINISH;
        $oTask->options = serialize($this->getOptions());
        $oTask->save();

        return $this;
    }

    /**
     * Additional logic for an implemented task
     * @return void
     */
    protected function _doFinish()
    { }

    /**
     * Set task to error
     * @param Exception $oException
     * @return AM_Task_Worker_Abstract
     * @throws AM_Task_Worker_Exception
     */
    public final function error($oException = null)
    {
        $oTask = $this->_getTask();

        if (is_null($oTask->id)) {
            throw new AM_Task_Worker_Exception('Trying to error none created task');
        }

        $this->_doError();

        if (!is_null($oException) && $oException instanceof Exception) {
            $this->addOption('error_code', $oException->getCode());
            $this->addOption('error_message', $oException->getMessage());
        }

        $oTask->options = serialize($this->getOptions());
        $oTask->status  = self::STATUS_ERROR;
        $oTask->save();

        $this->getLogger()->debug(sprintf('Task #%s set to error', $oTask->id));

        return $this;
    }

    /**
     * Additional logic for an implemented task
     * @return void
     */
    protected function _doError()
    { }

    /**
     * Create new task
     * @return AM_Task_Worker_Abstract
     */
    public final function create()
    {
        $oTask               = $this->_getTask();
        $oTask->options      = serialize($this->getOptions());
        $oTask->status       = self::STATUS_NEW;
        $oTask->task_type_id = $this->_getTaskType()->id;
        $oTask->save();

        $this->getLogger()->debug(sprintf('New task #%s [%s] created', $oTask->id, $this->_getTaskType()->class));

        return $this;
    }

    /**
     * Run task
     * @return AM_Task_Worker_Abstract
     */
    public final function run()
    {
         $oTask          = $this->_getTask();
         $oTask->status  = self::STATUS_RUN;
         $oTask->options = serialize($this->getOptions());
         $oTask->save();

         $this->_fire();

         return $this;
    }

    /**
     * Additional logic for an implemented task
     * @return void
     */
    abstract protected function _fire();
}

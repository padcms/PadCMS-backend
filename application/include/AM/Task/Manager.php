<?php
/**
 * @file
 * AM_Task_Manager class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Background tasks manager
 * @ingroup AM_Task
 */
class AM_Task_Manager implements AM_Task_Manager_Interface
{
    /** @var Zend_Log **/
    protected $_oLogger = null; /**< @type Zend_Log */

    /**
     * Get logger
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
     * Set logger
     * @param Zend_Log $oLogger
     * @return AM_Task_Manager
     */
    public function setLogger(Zend_Log $oLogger)
    {
        $this->_oLogger = $oLogger;

        return $this;
    }

    /**
     * Get new task and run it
     * @return void
     */
    public function run()
    {
        $oTask = AM_Model_Db_Table_Abstract::factory('task')
                ->findOneBy(array('status' => AM_Task_Worker_Abstract::STATUS_NEW));
        /* @var $oTask AM_Model_Db_Task */

        if (is_null($oTask)) {
            $this->getLogger()->info('There are no tasks to execute');
            return;
        }

        try {
            $this->getLogger()->debug('Running task '
                    . sprintf('#%s with type #%s', $oTask->id, $oTask->task_type_id));

            $oWorker = $oTask->getWorker();
            /* @var $oWorker AM_Task_Worker_Abstract */

            $this->getLogger()->debug('Task worker is '
                    . sprintf('%s', get_class($oWorker)));

            $oWorker->run();

            $this->getLogger()->debug(sprintf('Finishing task #%s', $oTask->id));

            $oWorker->finish();

            $this->getLogger()->debug(sprintf('Task #%s is finished', $oTask->id));

        } catch (Exception $e) {
            $this->getLogger()->crit(sprintf('Task #%s has an error', $oTask->id));
            $this->getLogger()->crit($e);

            $oWorker->error($e);
            return;
        }
    }

    public function error()
    {}
}
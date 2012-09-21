<?php
/**
 * @file
 * AM_Task_Manager class definition.
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
 * Background tasks manager
 * @ingroup AM_Task
 */
class AM_Task_Manager implements AM_Task_Manager_Interface
{
    const DAEMON_SLEEP_TIME = 15; //Time in seconds between task checking operations
    const DAEMON_MEMORY_LIMIT = 536870912; //512MB

    /** @var Zend_Log **/
    protected $_oLogger = null; /**< @type Zend_Log */
    /** @var Zend_Config **/
    protected $_oConfig = null; /**< @type Zend_Config */
    /** @var integer **/
    protected $_iProcessPid = null; /**< @type integer */
    /** @var boolean **/
    protected $_bStopDaemon = false; /**< @type boolean */

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
     *  Get path of the pid file
     * @return string
     */
    protected function _getPidFilePath()
    {
        $sPidFilePath = $this->getConfig()->temp->base . DIRECTORY_SEPARATOR . 'padcms_manager.pid';

        return $sPidFilePath;
    }

    /**
     * Saves the pid of the process
     * @return AM_Task_Manager
     */
    protected function _createPidFile()
    {
        $sPidFilePath = $this->_getPidFilePath();
        file_put_contents($sPidFilePath, getmypid());

        return $this;
    }

    /**
     * Removes the task manager daemon
     * @return AM_Task_Manager
     * @throws AM_Task_Manager_Exception
     */
    protected function _removePidFile()
    {
        if (!AM_Tools_Standard::getInstance()->unlink($this->_getPidFilePath())) {
            //Can't unlink pid file
            throw new AM_Task_Manager_Exception('Can\'t delete pidfile');
        }

        return $this;
    }

    /**
     * Check the pid file:
     *  if file exists, get the pid from file and find the process with this pid
     * @return boolean
     * @throws AM_Task_Manager_Exception
     */
    protected function _isDemonized()
    {
        $sPidFilePath = $this->_getPidFilePath();

        if (AM_Tools_Standard::getInstance()->is_file($sPidFilePath)) {
            $iPid = intval(file_get_contents($sPidFilePath));
            //checking for the presence of the process
            if (posix_kill($iPid, 0)) {
                //the daemon already run

                return true;
            } else {
                //pid file exists, but there isn't process
                $this->_removePidFile();
            }
        }

        return false;
    }

    /**
     * Habdler for unix sygnals
     * @param int $iSignalNumber
     */
    public function signalHandler($iSignalNumber)
    {
        switch($iSignalNumber) {
            case SIGQUIT:
            case SIGTERM:
                $this->_bStopDaemon = true;
                $this->getLogger()->debug('SIGTERM catched. Stopping the daemon');
                break;
            default:
                $this->getLogger()->debug(sprintf('Signal %d catched', $iSignalNumber));
        }
    }

    /**
     * Demonize the process
     */
    public function demonize()
    {
        if ($this->_isDemonized()) {
            $this->getLogger()->debug('Process already demonized');
            exit;
        }

        $this->_iProcessPid = pcntl_fork();

        if (-1 == $this->_iProcessPid) {
            throw new AM_Task_Manager_Exception('Can\'t demonize');
        } else if ($this->_iProcessPid > 0) {
            //Killing the root process
            exit;
        }

        $this->_createPidFile();
        //Make the current process a session leader
        posix_setsid();

        $this->_bootstrap();

        $this->getLogger()->debug('Process demonized');
    }

    /**
     * @return AM_Task_Manager
     */
    protected function _bootstrap()
    {
        $oApplication = new Zend_Application(
                        'daemon_' . APPLICATION_ENV,
                        APPLICATION_PATH . '/configs/application.ini'
        );

        $oApplication->bootstrap()
                ->run();

        return $this;
    }

    /**
     * Checks the memory usage
     * @return boolean
     */
    protected function _checkMemoryLimit()
    {
        if (memory_get_usage() > self::DAEMON_MEMORY_LIMIT) {
            return false;
        }

        return true;
    }

    /**
     * Get new task and run it
     * @return void
     */
    public function run()
    {
        try {
            $this->demonize();
        } catch (Exception $oException) {
            $this->getLogger()->crit($oException->getMessage());
        }

        declare(ticks=1);
        pcntl_signal(SIGTERM, array($this, 'signalHandler'));
        pcntl_signal(SIGQUIT, array($this, 'signalHandler'));

        while (!$this->_bStopDaemon && $this->_checkMemoryLimit()) {
            sleep(self::DAEMON_SLEEP_TIME);

            $this->getLogger()->debug(sprintf("Memory: %s", memory_get_usage(true)));
            $this->getLogger()->debug(sprintf("Peak Memory: %s", memory_get_peak_usage(true)));

            $oTask = AM_Model_Db_Table_Abstract::factory('task')
                    ->findOneBy(array('status' => AM_Task_Worker_Abstract::STATUS_NEW));
            /* @var $oTask AM_Model_Db_Task */

            if (is_null($oTask)) {
                $this->getLogger()->debug('There are no tasks to execute');
                continue;
            }

            try {
                $this->getLogger()->debug(sprintf('Running task #%s with type #%s', $oTask->id, $oTask->task_type_id));

                $oWorker = $oTask->getWorker();
                /* @var $oWorker AM_Task_Worker_Abstract */

                $this->getLogger()->debug(sprintf('Task worker is  %s', get_class($oWorker)));

                $oWorker->run();

                $this->getLogger()->debug(sprintf('Finishing task #%s', $oTask->id));

                $oWorker->finish();

                $this->getLogger()->debug(sprintf('Task #%s is finished', $oTask->id));
            } catch (Exception $oException) {
                $this->getLogger()->crit(sprintf('Task #%s has an error', $oTask->id));
                $this->getLogger()->crit($oException);

                $oWorker->error($oException);
                continue;
            }
        }

        $this->stop();
    }

    public function stop()
    {
        $this->_removePidFile();
        $this->getLogger()->debug('Daemon stopped!');
    }
}
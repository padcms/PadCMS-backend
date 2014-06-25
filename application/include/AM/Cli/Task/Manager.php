<?php
/**
 * @file
 * AM_Cli_Task_Manager class definition.
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
 * The manager of background tasks
 * @ingroup AM_Cli
 */
class AM_Cli_Task_Manager extends AM_Cli_Task_Abstract
{
    protected function _configure()
    {}

    public function execute()
    {
//       $oTaskManager = new AM_Task_Manager();
//       $oTaskManager->run();

        $this->getLogger()->debug(sprintf("Memory: %s", memory_get_usage(true)));
        $this->getLogger()->debug(sprintf("Peak Memory: %s", memory_get_peak_usage(true)));

        $oTask = AM_Model_Db_Table_Abstract::factory('task')
            ->findOneBy(array('status' => AM_Task_Worker_Abstract::STATUS_NEW));
        /* @var $oTask AM_Model_Db_Task */

        if (is_null($oTask)) {
            $this->getLogger()->debug('There are no tasks to execute');
            return;
        }

        $this->getLogger()->debug(sprintf('Running task #%s with type #%s', $oTask->id, $oTask->task_type_id));

        try {
            $oWorker = $oTask->getWorker();
            /* @var $oWorker AM_Task_Worker_Abstract */

        } catch(Exception $oException) {
            $this->getLogger()->crit($oException);
            return;
        }

        try {
            $this->getLogger()->debug(sprintf('Task worker is  %s', get_class($oWorker)));

            $oWorker->run();

            $this->getLogger()->debug(sprintf('Finishing task #%s', $oTask->id));

            $oWorker->finish();

            $this->getLogger()->debug(sprintf('Task #%s is finished', $oTask->id));
        } catch (Exception $oException) {
            $this->getLogger()->crit(sprintf('Task #%s has an error', $oTask->id));
            $this->getLogger()->crit($oException);

            $oWorker->error($oException);
            return;
        }
    }
}
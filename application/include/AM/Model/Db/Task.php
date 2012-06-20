<?php
/**
 * @file
 * AM_Model_Db_Task class definition.
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
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Task model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Task extends AM_Model_Db_Abstract
{
    /**
     * Get tasks type
     * @return AM_Model_Db_TaskType | null
     */
    public function getTaskType()
    {
        if (empty($this->task_type_id)) {
            return null;
        }

        $oTaskType = AM_Model_Db_Table_Abstract::factory('task_type')
                ->findOneBy(array('id' => $this->task_type_id));

        return $oTaskType;
    }

    /**
     * Get worker object
     * @return null | AM_Task_Worker_Abstract
     */
    public function getWorker()
    {
        $oTaskType = $this->getTaskType();

        if (is_null($oTaskType)) {
            throw new AM_Model_Db_Exception(sprintf('Task "%s" has no type', $this->id));
        }

        $sWorkerClass = $oTaskType->class;

        if (!class_exists($sWorkerClass)) {
            throw new AM_Model_Db_Exception(sprintf('Task has incorrect type "%s"', $sWorkerClass));
        }

        /* @var $oWorker AM_Task_Worker_Abstract */
        $oWorker = new $sWorkerClass();
        $oWorker->setOptions($this->getOptions())
                ->setTaskType($oTaskType)
                ->setTask($this);

        return $oWorker;
    }

    /**
     * Get task options
     * @return array
     */
    public function getOptions()
    {
        $aOptions = array();
        try {
            if (!empty($this->options)) {
                $aOptions = (array) @unserialize($this->options);
            }
        } catch (Exception $e) {
            //@todo logging
        }

        return $aOptions;
    }
}

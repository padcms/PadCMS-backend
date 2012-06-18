<?php
/**
 * @file
 * AM_Model_Db_Task class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
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

<?php
/**
 * @file
 * AM_Task_Worker_Mock class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Worker stub
 * @ingroup AM_Task
 */
class AM_Task_Worker_Mock extends AM_Task_Worker_Abstract
{
    protected function _fire()
    {
        $this->_getTask()->options = serialize(array('do' => 'fire'));
        $this->_getTask()->save();
    }

    protected function _doFinish()
    {
        $this->addOption('do', 'finish');
    }

    protected function _doError()
    {
        $this->addOption('do', 'finish');
    }
}
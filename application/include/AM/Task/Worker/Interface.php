<?php
/**
 * @file
 * AM_Task_Worker_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Background task interface
 * @ingroup AM_Task
 */
interface AM_Task_Worker_Interface
{
    public function run();

    public function error();
}
<?php
/**
 * @file
 * AM_Task_Manager_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Task
 */

/**
 * Background tasks manager
 * @ingroup AM_Task
 */
interface AM_Task_Manager_Interface
{
    public function run();
}
<?php
/**
 * @file
 * AM_Cli_Task_Manager class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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
       $oTaskManager = new AM_Task_Manager();
       $oTaskManager->run();
    }
}
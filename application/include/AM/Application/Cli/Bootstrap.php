<?php
/**
 * @file
 * AM_Application_Cli_Bootstrap class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Application
 */
class AM_Application_Cli_Bootstrap extends AM_Application_Bootstrap
{
    /**
     * Run the cli application
     *
     * @return void
     */
    public function run()
    {
        $oLogger = $this->getResource('log');

        $iArgumentsAmount = $_SERVER['argc'];
        if (1 === $iArgumentsAmount) {
            echo 'Please specify the task to run' . PHP_EOL;
            $oLogger->err('Task not specified!');
            exit;
        }

        $aArguments = &$_SERVER['argv'];

        $aArguments = array_slice($aArguments, 1);

        $oFilter = new Zend_Filter();
        $oFilter->addFilter(new Zend_Filter_Word_UnderscoreToCamelCase());

        $sTaskClass = 'AM_Cli_Task_' . $oFilter->filter($aArguments[0]);

        if (!class_exists($sTaskClass)) {
            echo sprintf('Class %s not found', $sTaskClass) . PHP_EOL;
            $oLogger->err(sprintf('Class %s not found', $sTaskClass));
            exit;
        }

        try {
            $oTask = new $sTaskClass();
            $oTask->run();
        } catch (Exception $e) {
            $oLogger->err($e);
            exit;
        }
    }
}
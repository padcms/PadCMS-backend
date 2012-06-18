<?php
/**
 * @file
 * AM_Cli_Task_Export class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This task creates revision package
 * @ingroup AM_Cli
 */
class AM_Cli_Task_Export extends AM_Cli_Task_Abstract
{
    protected function _configure()
    {
        $this->addOption('revision', 'r', '=i', 'Revision ID to export');
        $this->addOption('from', 'f', '=i', 'Export revisions with ID > FROM');
    }

    public function execute()
    {
        $iIdRevision = intval($this->_getOption('revision'));
        $iIdFrom     = intval($this->_getOption('from')); //If this option is set, we are building packaged for issues with id > $iIdFrom
        $aRevisions  = array();

        if (!empty  ($iIdRevision)) {
            $aRevisions[] = AM_Model_Db_Table_Abstract::factory("revision")->findOneBy('id', intval($iIdRevision));
        } elseif(!empty($iIdFrom)) {
            $aRevisions = AM_Model_Db_Table_Abstract::factory("revision")->fetchAll(array('deleted = ?'=>'no', 'id > ?' => intval($iIdFrom)));
        } else {
            $aRevisions = AM_Model_Db_Table_Abstract::factory("revision")->fetchAll(array('deleted = ?'=>'no'));
        }

        $oExportHandler   = AM_Handler_Locator::getInstance()->getHandler('export');
        /* @var $oExportHandler AM_Handler_Export */
        foreach ($aRevisions as $oRevision) {
            $oExportHandler->exportRevision($oRevision);
        }
    }
}
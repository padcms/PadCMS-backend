<?php
/**
 * @file
 * AM_Cli_Task_Export class definition.
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
 * This task creates revision package
 * @ingroup AM_Cli
 */
class AM_Cli_Task_Export extends AM_Cli_Task_Abstract
{
    protected function _configure()
    {
        $this->addOption('revision', 'r', '=i', 'Revision ID to export');
        $this->addOption('from', 'f', '=i', 'Export revisions with ID > FROM');
        $this->addOption('issue', 'is', '=i', 'Export revisions with selected issue ID');
        $this->addOption('application', 'app', '=i', 'Export revisions with selected application ID');
    }

    public function execute()
    {
        $iIdRevision    = intval($this->_getOption('revision'));
        $iIdFrom        = intval($this->_getOption('from')); //If this option is set, we are building packaged for issues with id > $iIdFrom
        $iIssueId       = intval($this->_getOption('issue'));
        $iApplicationId = intval($this->_getOption('application'));

        $oQuery = AM_Model_Db_Table_Abstract::factory('term')
                ->select()
                ->setIntegrityCheck(false)
                ->from('revision')

                ->joinInner('issue', 'issue.id = revision.issue')
                ->joinInner('application', 'application.id = issue.application')
                ->joinInner('client', 'client.id = application.client')

                ->where('revision.deleted = ?', 'no')
                ->where('issue.deleted = ?', 'no')
                ->where('application.deleted = ?', 'no')
                ->where('client.deleted = ?', 'no')

                ->columns(array('id' => 'revision.id'))

                ->order('revision.id ASC');

        if ($iIdFrom > 0) {
            $oQuery->where('revision.id > ?', $iIdFrom);
        }

        if ($iIdRevision > 0) {
            $oQuery->where('revision.id = ?', $iIdRevision);
        }

        if ($iIssueId > 0) {
            $oQuery->where('issue.id = ?', $iIssueId);
        }

        if ($iApplicationId > 0) {
            $oQuery->where('application.id = ?', $iApplicationId);
        }

        $oRevisions = AM_Model_Db_Table_Abstract::factory('revision')->fetchAll($oQuery);

        $oExportHandler   = AM_Handler_Locator::getInstance()->getHandler('export');
        /* @var $oExportHandler AM_Handler_Export */
        foreach ($oRevisions as $oRevision) {
            $oExportHandler->exportRevision($oRevision);
        }
    }
}
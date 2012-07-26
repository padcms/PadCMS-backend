<?php
/**
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
 * Task creates thumbnail for horizontal pages
 * @ingroup AM_Cli
 */
class AM_Cli_Task_ResizeHzpages extends AM_Cli_Task_Resize_Abstract
{
    /** @var int */
    protected $_iFromIssueId = null; /**< @type int */
    /** @var int */
    protected $_iIssueId = null; /**< @type int */
    /** @var int */
    protected $_iApplicationId = null; /**< @type int */

    protected function _configure()
    {
        $this->addOption('from', 'fr', '=i', 'Risize horizontal pages with issue ID > FROM');
        $this->addOption('issue', 'is', '=i', 'Resize horizontal pages with selected issue ID');
        $this->addOption('application', 'app', '=i', 'Resize elements with selected application ID');
        $this->addOption('preset', 'pr', '=s', 'Resize elements using selected preset');
    }

    public function execute()
    {
        $this->_iFromIssueId   = intval($this->_getOption('from'));
        $this->_iIssueId       = intval($this->_getOption('issue'));
        $this->_iApplicationId = intval($this->_getOption('application'));
        $this->_sPreset        = (string) $this->_getOption('preset');

        $this->_oThumbnailer = AM_Handler_Locator::getInstance()->getHandler('thumbnail');

        $this->_echo('Resizing horizontal pages');
        $this->_resizeHorizontalPdfs();
    }

    /**
     * Resizes all horizontal pages
     */
    protected function _resizeHorizontalPdfs()
    {
        $oQuery = AM_Model_Db_Table_Abstract::factory('page_horisontal')
                ->select()
                ->setIntegrityCheck(false)
                ->from('page_horisontal')
                ->joinInner('issue', 'issue.id = page_horisontal.id_issue')
                ->joinInner('application', 'application.id = issue.application')
                ->joinInner('client', 'client.id = application.client')

                ->where('issue.deleted = ?', 'no')
                ->where('application.deleted = ?', 'no')
                ->where('client.deleted = ?', 'no')

                ->where('resource IS NOT NULL')

                ->order('page_horisontal.id_issue ASC');

        if ($this->_iFromIssueId > 0) {
            $oQuery->where('page_horisontal.id_issue > ?', $this->_iFromIssueId);
        }

        if ($this->_iIssueId > 0) {
            $oQuery->where('issue.id = ?', $this->_iIssueId);
        }

        if ($this->_iApplicationId > 0) {
            $oQuery->where('application.id = ?', $this->_iApplicationId);
        }

        $oPagesHorizaontal = AM_Model_Db_Table_Abstract::factory('page_horisontal')->fetchAll($oQuery);

        $iCounter = 0;
        foreach ($oPagesHorizaontal as $oPageHorizontal) {
            try {
                $this->_resizeImage($oPageHorizontal->resource, $oPageHorizontal->id_issue, AM_Model_Db_PageHorisontal::RESOURCE_TYPE, $oPageHorizontal->weight);
            } catch (Exception $oException) {
                $this->_echo(sprintf('%s', $oException->getMessage()), 'error');
            }

            if ($iCounter++ > 100) {
                $iCounter = 0;
                AM_Handler_Temp::getInstance()->end();
            }
        }
    }
}
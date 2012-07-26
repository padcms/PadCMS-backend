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
 * Task creates thumbnail for elements
 * @ingroup AM_Cli
 */
class AM_Cli_Task_ResizeElements extends AM_Cli_Task_Resize_Abstract
{
    /** @var int */
    protected $_iFromId = null; /**< @type int */
    /** @var int */
    protected $_iElementId = null; /**< @type int */
    /** @var int */
    protected $_iRevisionId = null; /**< @type int */
    /** @var int */
    protected $_iPageId = null; /**< @type int */
    /** @var int */
    protected $_iIssueId = null; /**< @type int */
    /** @var int */
    protected $_iApplicationId = null; /**< @type int */

    protected function _configure()
    {
        $this->addOption('from', 'fr', '=i', 'Resize element with ID > FROM');
        $this->addOption('element', 'el', '=i', 'Resize element with selected ID');
        $this->addOption('revision', 'rev', '=i', 'Resize elements with selected revision ID');
        $this->addOption('page', 'p', '=i', 'Resize elements with selected page ID');
        $this->addOption('issue', 'is', '=i', 'Resize elements with selected issue ID');
        $this->addOption('application', 'app', '=i', 'Resize elements with selected application ID');
        $this->addOption('preset', 'pr', '=s', 'Resize elements using selected preset');
    }

    public function execute()
    {
        $this->_iFromId        = intval($this->_getOption('from'));
        $this->_iElementId     = intval($this->_getOption('element'));
        $this->_iRevisionId    = intval($this->_getOption('revision'));
        $this->_iPageId        = intval($this->_getOption('page'));
        $this->_iIssueId       = intval($this->_getOption('issue'));
        $this->_iApplicationId = intval($this->_getOption('application'));
        $this->_sPreset        = (string) $this->_getOption('preset');

        $this->_oThumbnailer = AM_Handler_Locator::getInstance()->getHandler('thumbnail');

        $this->_echo('Resizing elements');
        $this->_resizeElements();
    }

    /**
     * Resizes elements
     */
    protected function _resizeElements()
    {
        $oQuery = AM_Model_Db_Table_Abstract::factory('element_data')
                ->select()
                ->setIntegrityCheck(false)
                ->from('element_data')
                ->joinInner('element', 'element.id = element_data.id_element')
                ->joinInner('page', 'page.id = element.page')
                ->joinInner('revision', 'revision.id = page.revision')
                ->joinInner('issue', 'issue.id = revision.issue')
                ->joinInner('application', 'application.id = issue.application')
                ->joinInner('client', 'client.id = application.client')
                ->where(sprintf('element_data.key_name IN ("%s", "%s", "%s")', AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE
                                                                , AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL
                                                                , AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL_SELECTED))
                ->where('page.deleted = ?', 'no')
                ->where('revision.deleted = ?', 'no')
                ->where('issue.deleted = ?', 'no')
                ->where('application.deleted = ?', 'no')
                ->where('client.deleted = ?', 'no')

                ->order('element_data.id_element ASC');
        /* @var $oQuery Zend_Db_Table_Select */

        if ($this->_iFromId > 0) {
            $oQuery->where('element_data.id_element > ?', $this->_iFromId);
        }

        if ($this->_iElementId > 0) {
            $oQuery->where('element_data.id_element = ?', $this->_iElementId);
        }

        if ($this->_iPageId > 0) {
            $oQuery->where('page.id = ?', $this->_iPageId);
        }

        if ($this->_iRevisionId > 0) {
            $oQuery->where('revision.id = ?', $this->_iRevisionId);
        }

        if ($this->_iIssueId > 0) {
            $oQuery->where('issue.id = ?', $this->_iIssueId);
        }

        if ($this->_iApplicationId > 0) {
            $oQuery->where('application.id = ?', $this->_iApplicationId);
        }

        $oElementDatas = AM_Model_Db_Table_Abstract::factory('element_data')->fetchAll($oQuery);

        $iCounter = 0;
        foreach ($oElementDatas as $oElementData) {
            try {
                $oData = $oElementData->getData();
                if (!is_null($oData) && method_exists($oData, 'getThumbnailPresetName')) {
                    $this->_resizeImage($oElementData->value, $oElementData->id_element, AM_Model_Db_Element_Data_Resource::TYPE, $oElementData->key_name, $oElementData->getData()->getThumbnailPresetName());
                }
            } catch (Exception $oException) {
                $this->_echo(sprintf('%s', $oException->getMessage()), 'error');
            }

            if ($iCounter++ > 100) {
                $iCounter = 0;
                AM_Handler_Temp::getInstance()->end();
            }
        }
    }

    /**
     * Resizes all TOC terms
     */
    protected function _resizeTOC()
    {
        $oQuery = AM_Model_Db_Table_Abstract::factory('term')
                ->select()
                ->where('(thumb_stripe IS NOT NULL OR thumb_summary IS NOT NULL) AND deleted = "no"');

        $oTerms = AM_Model_Db_Table_Abstract::factory('term')->fetchAll($oQuery);

        foreach ($oTerms as $oTerm) {
            try {
                if (!empty($oTerm->thumb_stripe)) {
                    $this->_resizeImage($oTerm->thumb_stripe, $oTerm->id, AM_Model_Db_Term_Data_Resource::TYPE, AM_Model_Db_Term_Data_Resource::RESOURCE_KEY_STRIPE);
                }
                if (!empty($oTerm->thumb_summary)) {
                    $this->_resizeImage($oTerm->thumb_summary, $oTerm->id, AM_Model_Db_Term_Data_Resource::TYPE, AM_Model_Db_Term_Data_Resource::RESOURCE_KEY_SUMMARY);
                }
            } catch (Exception $oException) {
                $this->_echo(sprintf('%s', $oException->getMessage()), 'error');
            }
        }
    }
}
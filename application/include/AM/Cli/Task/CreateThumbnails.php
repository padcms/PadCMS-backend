<?php
/**
 * @file
 * AM_Cli_Task_CreateThumbnails class definition.
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
 * This task creates thumbnail for all resources
 * @ingroup AM_Cli
 */
class AM_Cli_Task_CreateThumbnails extends AM_Cli_Task_Abstract
{
    /** @var AM_Handler_Thumbnail */
    protected $_oThumbnailer = null; /**< @type AM_Handler_Thumbnail */

    protected function _configure()
    {
        $this->addOption('from', 'f', '=i', 'Export element with ID > FROM');
        $this->addOption('element', 'el', '=i', 'Export element with selected ID');
    }

    public function execute()
    {
        $iIdFrom  = intval($this->_getOption('from')); //If this option is set, we are creating thumbnails for elements with ID > $iIdFrom
        $iElement = intval($this->_getOption('element')); //If this option is set, we are creating thumbnails for elements with ID > $iIdFrom

        $this->_oThumbnailer = AM_Handler_Locator::getInstance()->getHandler('thumbnail');

        $this->_echo('Resizing elements');
        $this->_resizeElements($iIdFrom, $iElement);

        if ($iElement > 0 ) return;

        $this->_echo('Resizing TOC');
        $this->_resizeTOC();

        $this->_echo('Resizing horizontal pdfs');
        $this->_resizeHorizontalPdfs();
    }

    /**
     * Resizes all elements with type "resource"
     */
    protected function _resizeElements($iIdFrom = null, $iElementId = null)
    {
        $oQuery = AM_Model_Db_Table_Abstract::factory('element_data')
                ->select()
                ->where(sprintf('key_name IN ("%s", "%s", "%s")', AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE
                                                                , AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL
                                                                , AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL_SELECTED))
                ->order('id_element ASC');

        if ($iIdFrom > 0) {
            $oQuery->where('id_element > ?', $iIdFrom);
        } elseif ($iElementId > 0) {
            $oQuery->where('id_element = ?', $iElementId);
        }

        $oElementDatas = AM_Model_Db_Table_Abstract::factory('element_data')->fetchAll($oQuery);

        foreach ($oElementDatas as $oElementData) {
            try {
                $oData = $oElementData->getData();
                if (!is_null($oData) && method_exists($oData, 'getThumbnailPresetName')) {
                    $this->_resizeImage($oElementData->value, $oElementData->id_element, AM_Model_Db_Element_Data_Resource::TYPE, $oElementData->key_name, $oElementData->getData()->getThumbnailPresetName());
                }
            } catch (Exception $oException) {
                $this->_echo(sprintf('%s', $oException->getMessage()), 'error');
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

    /**
     * Resizes all horizontal pages
     */
    protected function _resizeHorizontalPdfs()
    {
        $oQuery = AM_Model_Db_Table_Abstract::factory('page_horisontal')
                ->select()
                ->where('resource IS NOT NULL');

        $oPagesHorizaontal = AM_Model_Db_Table_Abstract::factory('page_horisontal')->fetchAll($oQuery);

        foreach ($oPagesHorizaontal as $oPageHorizontal) {
            try {
                $this->_resizeImage($oPageHorizontal->resource, $oPageHorizontal->id_issue, AM_Model_Db_PageHorisontal::RESOURCE_TYPE, $oPageHorizontal->weight);
            } catch (Exception $oException) {
                $this->_echo(sprintf('%s', $oException->getMessage()), 'error');
            }
        }
    }


    /**
     * Resizes given image
     * @param string $sFileBaseName
     * @param int $iElementId The id of element, term, horisontal page
     * @param string $sResourceType The type of resource's parent (element, toc, cache-static-pdf)
     * @param string $sResourceKeyName The name of the resource type (resource, thumbnail, etc)
     * @return @void
     */
    protected function _resizeImage($sFileBaseName, $iElementId, $sResourceType, $sResourceKeyName, $sResourcePresetName = null)
    {
        if (is_null($sResourcePresetName)) {
            $sResourcePresetName = $sResourceType;
        }

        $sFileExtension = strtolower(pathinfo($sFileBaseName, PATHINFO_EXTENSION));

        $sFilePath = AM_Tools::getContentPath($sResourceType, $iElementId)
                    . DIRECTORY_SEPARATOR
                    . $sResourceKeyName . '.' . $sFileExtension;

        $this->_oThumbnailer->clearSources()
                ->addSourceFile($sFilePath)
                ->loadAllPresets($sResourcePresetName)
                ->createThumbnails();

        $this->_echo(sprintf('%s', $sFilePath), 'success');
    }
}
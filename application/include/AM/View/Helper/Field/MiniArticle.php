<?php
/**
 * @file
 * AM_View_Helper_Field_MiniArticle class definition.
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
 * @ingroup AM_View_Helper
 */
class AM_View_Helper_Field_MiniArticle extends AM_View_Helper_Field
{
    public function show()
    {
        $aElements = $this->_oPage->getElementsByField($this->_oField);

        if (count($aElements)) {
            $aElementsView = array();
            foreach ($aElements as $oElement) {
                /* @var $oElement AM_Model_Db_Element */
                $aElementView = array (
                    'id' => $oElement->id
                );

                $sUniq = '?' . strtotime($oElement->updated);

                $sVideo = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_VIDEO);
                if (false !== $sVideo) {
                    $aElementView[AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_VIDEO] = $sVideo;
                }

                $sThumbnail = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL);
                if (false !== $sThumbnail) {
                    $sFileExtension = pathinfo($sThumbnail, PATHINFO_EXTENSION);

                    $aElementView['thumbnail']    = $sThumbnail;
                    $aElementView['thumbnailUri'] = AM_Tools::getImageUrl('none', 'element', $oElement->id,
                            AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL. '.' . $sFileExtension ). $sUniq;
                }

                $sThumbnailSelected = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL_SELECTED);
                if (false !== $sThumbnailSelected) {
                    $sFileExtension = pathinfo($sThumbnail, PATHINFO_EXTENSION);

                    $aElementView['thumbnailSelected']    = $sThumbnailSelected;
                    $aElementView['thumbnailSelectedUri'] = AM_Tools::getImageUrl('none', 'element', $oElement->id,
                            AM_Model_Db_Element_Data_MiniArticle::DATA_KEY_THUMBNAIL_SELECTED . '.' . $sFileExtension) . $sUniq;
                }

                $aResourceView = $this->_getResourceViewData($oElement);
                $aElementView  = array_merge($aElementView, $aResourceView);

                $aElementsView[] = $aElementView;
            }
        }

        $aFieldView = array();

        if (isset($aElementsView)) {
            $aFieldView['elements'] = $aElementsView;
        }

        $aExtensions = array_map('strtoupper', AM_Model_Db_Element_Data_MiniArticle::getAllowedFileExtensions());
        sort($aExtensions, SORT_STRING);
        $aFieldView['allowedExtensions'] = implode(' / ', $aExtensions);

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}

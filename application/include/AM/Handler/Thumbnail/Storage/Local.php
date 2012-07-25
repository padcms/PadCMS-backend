<?php

/*
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
 * @ingroup AM_Handler
 */
class AM_Handler_Thumbnail_Storage_Local extends AM_Handler_Thumbnail_Storage_Abstract
{
    /**
     * Saves all the resources to the local storage
     */
    public function save()
    {
        $sPath = $this->_getSavePath() . DIRECTORY_SEPARATOR . $this->getPathPrefix();
        AM_Tools_Standard::getInstance()->mkdir($sPath, octdec($this->getConfig()->thumbnailDirChmod), true);

        foreach ($this->getResources() as $sResource) {
            $sDestinationFile = $sPath . DIRECTORY_SEPARATOR . pathinfo($sResource, PATHINFO_BASENAME);
            AM_Tools_Standard::getInstance()->copy($sResource, $sDestinationFile);
            AM_Tools_Standard::getInstance()->chmod($sDestinationFile, octdec($this->getConfig()->thumbnailFileChmod));
        }

        $this->_aResources = array();
    }

    /**
     * Returns image's URL
     * @param string $sPreset
     * @param string $sType
     * @param int $iId
     * @param string $sFileName
     * @return string $sImageUrl
     */
    public function getResourceUrl($sPreset, $sType, $iId, $sFileName)
    {
        $sThumbailUri = trim($this->getConfig()->thumbnailUrl, '/');

        $sImageUrl = '/' . $sThumbailUri
            . '/' . (string) $sPreset
            . '/' . (string) $sType
            . '/' . AM_Tools_String::generatePathFromId(intval($iId), '/')
            . '/' . (string) $sFileName;

        return $sImageUrl;
    }

    /**
     * Removes resources
     * @param string $sResourceType
     * @param int $iId
     * @param string $sFileName
     */
    public function clearResources($sFileName = null)
    {
        $sPath = $this->_getSavePath() . DIRECTORY_SEPARATOR . $this->getPathPrefix();

        if (!empty($sFileName)) {
            $aFiles = glob($sPath . DIRECTORY_SEPARATOR . $sFileName);
            if ($aFiles) {
                foreach ($aFiles as $sFile) {
                    AM_Tools_Standard::getInstance()->unlink($sFile);
                }
            }
            return;
        }

        AM_Tools_Standard::getInstance()->clearDir($sPath);
    }

    /**
     * Returns path to the resource's folder
     * @return string
     */
    private function _getSavePath()
    {
        $sPath = rtrim($this->getConfig()->thumbnailFolder, DIRECTORY_SEPARATOR);

        return $sPath;
    }
}
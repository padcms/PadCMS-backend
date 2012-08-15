<?php
/**
 * @file
 * AM_Resource_Concrete_Pdf class definition.
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
 * @ingroup AM_Resource
 */
class AM_Resource_Concrete_Pdf extends AM_Resource_Abstract
{
    /** @var string Path to the png version of pdf */
    protected $_sFileForThumbnail = null; /**< @type string */

    /**
     * Returns path to the padcmsdraw tool
     * @return string
     */
    protected function _getPadcmsdrawPath()
    {
        $sPath = $this->getConfig()->bin->get('padcmsdraw', '/usr/local/bin/padcmsdraw');

        return $sPath;
    }

    /**
     * Get file wich will be resized for thumbnail
     * @return string Path to file
     * @throws AM_Resource_Exception
     */
    public function getFileForThumbnail()
    {
        if (!is_null($this->_sFileForThumbnail)) {
            return $this->_sFileForThumbnail;
        }

        $sTempDir    = AM_Handler_Temp::getInstance()->getDir();
        $sPdfDrawBin = $this->_getPadcmsdrawPath();

        $sCmd = sprintf('nice -n 15 %s -a -r 200 -o %s/splitted-%%d.png %s 1 > /dev/null 2>&1', $sPdfDrawBin, $sTempDir, $this->_sSourceFile);

        AM_Tools_Standard::getInstance()->passthru($sCmd);

        $aFiles = (array) AM_Tools_Finder::type('file')
                ->name('splitted-*.png')
                ->sort_by_name()
                ->in($sTempDir);

        if (empty($aFiles)) {
            throw new AM_Resource_Exception('Can\'t covert PDF to PNG. Temp file not found');
        }

        $this->_sFileForThumbnail = $aFiles[0];

        return $this->_sFileForThumbnail;
    }

    /**
     * Convert pdf to png files
     * @return type
     */
    public function getAllPagesAsPng()
    {
        $sTempDir    = AM_Handler_Temp::getInstance()->getDir();
        $sPdfDrawBin = $this->_getPadcmsdrawPath();

        $sCmd = sprintf('nice -n 15 %s -a -r 200 -o %s/splitted-%%d.png %s > /dev/null 2>&1', $sPdfDrawBin, $sTempDir, $this->_sSourceFile);

        AM_Tools_Standard::getInstance()->passthru($sCmd);

        $aFiles = AM_Tools_Finder::type('file')
                ->name('splitted-*.png')
                ->sort_by_name()
                ->in($sTempDir);

        return $aFiles;
    }

    /**
     * Get information from pdf
     * @return string Information about PDF in JSON {'widht', 'height', 'zones' => [{'uri','top','left','width','height'}]
     * @throws AM_Resource_Exception
     */
    public function getPdfInfo()
    {
        $sPdfDrawBin = $this->_getPadcmsdrawPath();

        $sCmd = sprintf('nice -n 15 %s -j %s 2>&1', $sPdfDrawBin, $this->_sSourceFile);

        AM_Tools_Standard::getInstance()->exec($sCmd, $aCommandOutput);
        if (!$aCommandOutput || count($aCommandOutput) == 0) {
            throw new AM_Resource_Exception('Unable to get info from file ' . $this->_sSourceFile);
        }

        $aPageInfo = implode('', $aCommandOutput);

        return $aPageInfo;
    }
}
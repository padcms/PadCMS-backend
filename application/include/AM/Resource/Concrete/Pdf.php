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
     * Get file wich will be resized for thumbnail
     * @return string Path to file
     * @throws AM_Resource_Exception
     */
    public function getFileForThumbnail()
    {
        if (!is_null($this->_sFileForThumbnail)) {
            return $this->_sFileForThumbnail;
        }

        $sTempDir = AM_Handler_Temp::getInstance()->getDir();
        $oConfig = $this->getConfig();
        /* @var $oConfig Zend_Config */
        $sPdfDrawBin = $oConfig->bin->get('pdfdraw', '/usr/bin/pdfdraw');

        $sCmd = sprintf('nice -n 15 %s -a -r 200 -o %s/splitted-%s.png %s 1 > /dev/null 2>&1', $sPdfDrawBin, $sTempDir, '%d', $this->_sSourceFile);

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
        $sPdfDrawBin = $this->getConfig()->bin->get('pdfdraw', '/usr/bin/pdfdraw');

        $sCmd = sprintf('nice -n 15 %s -a -r 200 -o %s/splitted-%s.png %s > /dev/null 2>&1', $sPdfDrawBin, $sTempDir, '%d', $this->_sSourceFile);

        AM_Tools_Standard::getInstance()->passthru($sCmd);

        $aFiles = AM_Tools_Finder::type('file')
                ->name('splitted-*.png')
                ->sort_by_name()
                ->in($sTempDir);

        return $aFiles;
    }

    /**
     * Get information from pdf
     * @return array Information about PDF {'widht', 'height', 'zones' => [{'uri','top','left','width','height'}]
     * @throws AM_Resource_Exception
     */
    public function getPdfInfo()
    {
        $aPageInfo = array();

        $aPageInfo          = $this->_getPageSize();
        $aPageInfo['zones'] = $this->_getActiveZones();
        $aPageInfo['text']  = $this->_getText();

        return $aPageInfo;
    }

    /**
     * Gets page width and height from pdfinfo.
     *
     * @author dmitry.goretsky
     * @throws AM_Resource_Exception
     * @return array Page width and height
     */
    protected function _getPageSize()
    {
        $aCommandOutput    = array();
        $aPageSize         = array();
        $aMatches          = array();
        $sSizeMatchPattern = "@Page size:\s+(?P<width>[0-9]*\.?[0-9]*+)\s.\s(?P<height>[0-9]*\.?[0-9]*+).+@";

        AM_Tools_Standard::getInstance()->exec('pdfinfo ' . $this->_sSourceFile, $aCommandOutput);
        if (!$aCommandOutput || count($aCommandOutput) == 0) {
            throw new AM_Resource_Exception('Unable to get info from file ' . $this->_sSourceFile);
        }

        foreach ($aCommandOutput as $sOutputLine) {
            preg_match($sSizeMatchPattern, $sOutputLine, $aMatches);
            if ($aMatches) {
                $aPageSize['width'] = $aMatches['width'];
                $aPageSize['height'] = $aMatches['height'];
                break;
            }
        }

        return $aPageSize;
    }

    /**
     * Gets active zones coords and content URI
     *
     * @author dmitry.goretsky
     * @throws AM_Resource_Exception
     * @return array Active zone coords and content URIs list.
     */
    protected function _getActiveZones()
    {
        $aActiveZones = array();
        $aMatches = array();
        $aParsedData = array();

        //Runes to get zones coords and objects' IDs with URLs
        $sObjectSearchPattern = "@[\d]+.[\d]{1}.obj\s+<</A\s(?P<ObjectId>[\d]+)\s.+\[(?P<Coords>(?:[-+]?[\d]+\.?([\d]+)?\s){3}(?:[-+]?[\d]+\.?([\d]+)?){1})\].+/Annot>>\s+endobj@iU";
        $sFileContents = file_get_contents($this->_sSourceFile);

        if (!$sFileContents) {
            throw new AM_Resource_Exception('Unable to read file ' . $this->_sSourceFile);
        }

        preg_match_all($sObjectSearchPattern, $sFileContents, $aMatches);

        if (empty($aMatches['ObjectId'])) {
            $sObjectSearchPattern = "@[\d]+.[\d]{1}.obj\s+<</Rect\[(?P<Coords>(?:[-+]?[\d]+\.?([\d]+)?\s){3}(?:[-+]?[\d]+\.?([\d]+)?){1})\].+/A\s(?P<ObjectId>[\d]+)\s.+/Annot>>\s+endobj@iU";
            preg_match_all($sObjectSearchPattern, $sFileContents, $aMatches);
        }

        if (!empty($aMatches['ObjectId']) && !empty($aMatches['Coords'])) {
            $aParsedData = array_combine($aMatches['ObjectId'], $aMatches['Coords']);
            foreach ($aParsedData as $iObjectId => $sCoords) {
                //Runes to get location url by object ID
                $sUrlSearchPattern = "@{$iObjectId}\s[\d]{1}.obj\s<</(.+)?URI\((?P<Uri>.+)\)@iU";
                preg_match($sUrlSearchPattern, $sFileContents, $aMatches);
                if (!empty($aMatches)) {
                    $aCoords = explode(' ', $sCoords);
                    $aActiveZones[] = array(
                        'uri' => $aMatches['Uri'],
                        'llx' => $aCoords[0],
                        'lly' => $aCoords[1],
                        'urx' => $aCoords[2],
                        'ury' => $aCoords[3]
                    );
                }
            }
        }

        return $aActiveZones;
    }

    /**
     * Gets page text content
     *
     * @author dmitry.goretsky
     * @return string Page text
     * @throws AM_Resource_Exception
     */
    protected function _getText()
    {
        $sOutput = '';

        $sTempFilePath  = AM_Handler_Temp::getInstance()->getFile('pdftotext');
        $sPdfToTextPath = $this->getConfig()->bin->get('pdftotext', '/usr/bin/pdftotext');
        $sCommand       = sprintf('nice -n 15 %s -l 1 %s %s', $sPdfToTextPath, $this->_sSourceFile, $sTempFilePath);
        AM_Tools_Standard::getInstance()->passthru($sCommand);
        $sOutput = file_get_contents($sTempFilePath);

        if (!$sOutput) {
            throw new AM_Resource_Exception('Unable to get page text');
        }

        return $sOutput;
    }
}
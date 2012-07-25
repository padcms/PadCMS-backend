<?php
/**
 * @file
 * AM_Handler_Export class definition.
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
 * Horisontal pdfs handler
 *
 * @ingroup AM_Handler
 */
class AM_Handler_HorisontalPdf extends AM_Handler_Abstract
{
    /** AM_Model_Db_Issue **/
    protected $_oIssue     = null; /**< @type AM_Model_Db_Issue */

    /**
     * Set issue instance
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Handler_HorisontalPdf
     * @throws AM_Handler_HorisontalPdf_Exception
     */
    public function setIssue(AM_Model_Db_Issue $oIssue)
    {
        $this->_oIssue = $oIssue;

        return $this;
    }

    /**
     * Get issue instance
     * @return AM_Model_Db_Issue
     * @throws AM_Handler_HorisontalPdf_Exception
     */
    public function getIssue()
    {
        if (is_null($this->_oIssue)) {
            throw new AM_Handler_HorisontalPdf_Exception('Set issue before get it');
        }

        return $this->_oIssue;
    }

    /**
     * Compile static pdf
     * @return AM_Handler_HorisontalPdf
     * @throws AM_Handler_HorisontalPdf_Exception
     */
    public function compile()
    {
        AM_Model_Db_Table_Abstract::factory('page_horisontal')->deleteBy(array('id_issue' => $this->getIssue()->id));

        AM_Tools::clearContent(AM_Model_Db_StaticPdf_Data_Abstract::TYPE_CACHE, $this->getIssue()->id);
        AM_Tools::clearResizerCache(AM_Model_Db_StaticPdf_Data_Abstract::TYPE_CACHE, AM_Model_Db_StaticPdf_Data_Abstract::TYPE_CACHE, $this->getIssue()->id);

        if (!count($this->getIssue()->getHorizontalPdfs())) {
            return $this;
        }

        switch ($this->getIssue()->static_pdf_mode) {
            case AM_Model_Db_Issue::HORISONTAL_MODE_PAGE:
                //Get first pages of all pdfs and glue them
                $aFiles = (array) $this->_compilePageVersion();
                break;
            case AM_Model_Db_Issue::HORISONTAL_MODE_ISSUE:
                //Get pdf's first pages and glue them
                $aFiles = (array) $this->_compileIssueVersion();
                break;
            case AM_Model_Db_Issue::HORISONTAL_MODE_2PAGES:
                //Get all pdf's pages and glue them
                $aFiles = (array) $this->_compile2PagesVersion();
                break;
        }

        $sCachePath = $this->_getFilesPath();
        if (!empty($aFiles)) {
            if (!AM_Tools_Standard::getInstance()->is_dir($sCachePath)) {
                if (!AM_Tools_Standard::getInstance()->mkdir($sCachePath, 0777, true)) {
                    throw new AM_Handler_HorisontalPdf_Exception('I/O error while create cache dir');
                }
            }
            $oThumbnailer = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
            /* @var $oThumbnailer AM_Handler_Thumbnail */
            $oThumbnailer->clearSources()->loadAllPresets(AM_Model_Db_StaticPdf_Data_Abstract::TYPE_CACHE);

            foreach ($aFiles as $sFile) {
                $sFilename    = pathinfo($sFile, PATHINFO_BASENAME);
                $sNewFilePath = $sCachePath . DIRECTORY_SEPARATOR . $sFilename;
                if (!AM_Tools_Standard::getInstance()->rename($sFile, $sNewFilePath)) {
                    throw new AM_Handler_HorisontalPdf_Exception('I/O error while copy file');
                }
                AM_Tools_Standard::getInstance()->chmod($sNewFilePath, 0666);
                if (AM_Tools::isAllowedImageExtension($sNewFilePath)) {
                    $oThumbnailer->addSourceFile($sNewFilePath);
                }

                $oPageHorisontal = new AM_Model_Db_PageHorisontal();
                $oPageHorisontal->id_issue = $this->getIssue()->id;
                $oPageHorisontal->resource = $sFilename;
                $oPageHorisontal->weight   = pathinfo($sFile, PATHINFO_FILENAME);
                $oPageHorisontal->save();
            }
            $oThumbnailer->createThumbnails();
        }

        return $this;
    }

    /**
     * Glue pdfs pages
     * @return array
     */
    protected function _compileIssueVersion()
    {
        $aConvertedPdfs = array();
        $oStaticPdf     = $this->getIssue()->getHorizontalPdfs()->current();
        //Get first page of PDF, connver to the PNG and push to the stack
        $aConvertedPdfs[] = $oStaticPdf->getFirstPageAsPng();

        $aFiles = $this->_glueImages($aConvertedPdfs);

        return $aFiles;
    }

    /**
     * Glue pdfs pages
     * @return array
     */
    protected function _compilePageVersion()
    {
        $aConvertedPdfs = array();
        //Get all static pdfs sorted by weight
        foreach ($this->getIssue()->getHorizontalPdfs() as $oStaticPdf) {
            /* @var $oStaticPdf AM_Model_Db_StaticPdf */
            //Get first page for each PDF, connver to the PNG and push to the stack
            $aConvertedPdfs[] = $oStaticPdf->getFirstPageAsPng();
        }

        $aFiles = $this->_glueImages($aConvertedPdfs);

        return $aFiles;
    }

    /**
     * Glue pdfs pages
     * @return array
     */
    protected function _compile2PagesVersion()
    {
        $aConvertedPdfs = array();
        $oStaticPdf      = $this->getIssue()->getHorizontalPdfs()->current();
        //Get first page of PDF, connver to the PNG and push to the stack
        $aConvertedPdfs = $oStaticPdf->getAllPagesAsPng();

        $aFiles = $this->_glueImages($aConvertedPdfs);

        return $aFiles;
    }

    /**
     * Glue pdfs pages
     * @todo: refactoring
     * @return array
     */
    protected function _glueImages($aConvertedPdfs)
    {
        $sTempDir = AM_Handler_Temp::getInstance()->getDir();

        $aTargetFiles = array();
        $oTargetImage = new Imagick();
        $oPage1Image  = new Imagick();
        $oPage2Iamge  = new Imagick();

        $iPageTargetCounter = 0;

        for ($i = 0; $i < count($aConvertedPdfs); $i++) {
            $oPage1Image       = new Imagick($aConvertedPdfs[$i]);
            $page1Width  = $oPage1Image->getImageWidth();
            $page1Height = $oPage1Image->getImageHeight();

            if ($page1Width > $page1Height) {
                $oTargetImage = $oPage1Image;
                $this->_writeImage($oTargetImage, $aTargetFiles, $sTempDir, $iPageTargetCounter);
                $iPageTargetCounter++;
            } else {
                $bHasNextPage = false;
                $iPage2Width  = null;
                $oPage2Height = null;

                if ($i + 1 < count($aConvertedPdfs)) {
                    $bHasNextPage = true;
                    $i++;
                    $oPage2Iamge = new Imagick($aConvertedPdfs[$i]);
                    $iPage2Width = $oPage2Iamge->getImageWidth();
                    $oPage2Height = $oPage2Iamge->getImageHeight();
                }

                if (!$bHasNextPage || $iPage2Width > $oPage2Height) {
                    $oTargetImage->newImage($page1Width * 2, $page1Height, 'none');
                    $iOffset = 0;
                    if (!$bHasNextPage) {
                        $iOffset = 0;
                    } elseif ($i == 1) {
                        $iOffset = $page1Width;
                    } elseif ($i - 1 > 0) {
                        $iOffset = $page1Width / 2;
                    }
                    $oTargetImage->compositeImage($oPage1Image, imagick::COMPOSITE_COPY, $iOffset, 0);
                    $this->_writeImage($oTargetImage, $aTargetFiles, $sTempDir, $iPageTargetCounter);
                    $iPageTargetCounter++;

                    if ($bHasNextPage) {
                        $oTargetImage = $oPage2Iamge;
                        $this->_writeImage($oTargetImage, $aTargetFiles, $sTempDir, $iPageTargetCounter);
                        $iPageTargetCounter++;
                    }
                } else {
                    $oTargetImage->newImage($page1Width + $iPage2Width, $page1Height, 'none');
                    $oTargetImage->compositeImage($oPage1Image, imagick::COMPOSITE_COPY, 0, 0);
                    $oTargetImage->compositeImage($oPage2Iamge, imagick::COMPOSITE_COPY, $page1Width, 0);
                    $this->_writeImage($oTargetImage, $aTargetFiles, $sTempDir, $iPageTargetCounter);
                    $iPageTargetCounter++;
                }
            }

            $oTargetImage->clear();
            $oPage1Image->clear();
            $oPage2Iamge->clear();
        }

        return $aTargetFiles;
    }

    /**
     * Write image
     * @todo: refactoring
     * @param Imagick $oTargetImage
     * @param Imagick $aTargetFiles
     * @param string $sTempDir
     * @param int $iPageCounter
     */
    protected function _writeImage(&$oTargetImage, &$aTargetFiles, &$sTempDir, &$iPageCounter)
    {
        $sImagePath = $sTempDir . '/' . ($iPageCounter + 1) . '.png';

        $oTargetImage->setImageFormat('png');
        $oTargetImage->setImageCompressionQuality(100);
        $oTargetImage->writeImage($sImagePath);

        $aTargetFiles[] = $sImagePath;
    }

    /**
     * Returns archive path
     * @return null|string
     */
    public function getArchive()
    {
        $sZipFileName = sprintf('static-pdf-%s-%s.zip', $this->getIssue()->id, strtotime($this->getIssue()->updated));
        $sZipFileDir  = $this->getConfig()->temp->base . DIRECTORY_SEPARATOR . 'static-pdf';
        $sZipFile     = $sZipFileDir . DIRECTORY_SEPARATOR . $sZipFileName;

        if (!AM_Tools_Standard::getInstance()->is_file($sZipFile)) {
            $sTmpZipFile = AM_Handler_Temp::getInstance()->getFile($sZipFileName);

            $oZip = new ZipArchive();
            $oZip->open($sTmpZipFile, ZIPARCHIVE::CREATE);

            $aFiles = $this->getFiles();

            if (empty($aFiles)) {
                return null;
            }

            foreach ($aFiles as $sFile) {
                $oZip->addFile($sFile, pathinfo($sFile, PATHINFO_BASENAME));
            }

            $oZip->close();

            if (!AM_Tools_Standard::getInstance()->is_dir($sZipFileDir)) {
                AM_Tools_Standard::getInstance()->mkdir($sZipFileDir, 0777);
            }

            AM_Tools_Standard::getInstance()->copy($sTmpZipFile, $sZipFile);
        }

        return $sZipFile;
    }

    /**
     * Get compilled files
     * @param string|null $sName Name pattern ('*_thumb.*' return all thumbnails of static pdfs)
     * @return type
     */
    public function getFiles($sName = null)
    {
        $sName = is_null($sName)? "*" : $sName;

        $aFiles = AM_Tools_Finder::type('file')
                ->name($sName)
                ->sort_by_name()
                ->in($this->_getFilesPath());

        return $aFiles;
    }

    /**
     * Get path to locate static pdfs compilled files
     * @return string
     */
    protected function _getFilesPath()
    {
        $sPath = AM_Tools::getContentPath('cache-static-pdf', $this->getIssue()->id);

        return $sPath;
    }
}
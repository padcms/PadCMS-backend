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

class ThumbnailStorageLocalTest extends PHPUnit_Framework_TestCase
{
    const THUMBNAIL_FOLDER     = '/thumbnail/folder';
    const THUMBNAIL_FILE_CHMOD = 0666;
    const THUMBNAIL_DIR_CHMOD  = 0777;
    const PATH_PREFIX          = 'path/prefix';
    const RESOURCE_PATH        = '/path/to/resource';
    const RESOURCE_FILE        = 'resource.ext';

    /** @var AM_Handler_Thumbnail_Storage_Local */
    public $oStorage = null; /**<@type AM_Handler_Thumbnail_Storage_Local */
    /** @var AM_Tools_Standard */
    public $oStandardMock = null; /**<@type AM_Tools_Standard */

    public function setUp()
    {
        $this->oStandardMock = $this->getMock('AM_Tools_Standard', array('is_file', 'mkdir', 'copy', 'chmod'));

        $oThumbnailHandler = AM_Handler_Locator::getInstance()->getHandler('thumbnail');

        $aConfig = array(
            'thumbnailFolder'    => self::THUMBNAIL_FOLDER,
            'thumbnailFileChmod' => self::THUMBNAIL_FILE_CHMOD,
            'thumbnailDirChmod'  => self::THUMBNAIL_DIR_CHMOD
        );

        $this->oStorage = new AM_Handler_Thumbnail_Storage_Local($oThumbnailHandler, new Zend_Config($aConfig));
        $this->oStorage->setPathPrefix(self::PATH_PREFIX);
    }

    public function testShouldSaveResource()
    {
        $sResource   = self::RESOURCE_PATH . DIRECTORY_SEPARATOR . self::RESOURCE_FILE;
        $sThumbsPath = self::THUMBNAIL_FOLDER . DIRECTORY_SEPARATOR . self::PATH_PREFIX;

        //THEN
        $this->oStandardMock->expects($this->once())
             ->method('is_file')
             ->with($this->equalTo($sResource))
             ->will($this->returnValue(true));

        $this->oStandardMock->expects($this->once())
             ->method('mkdir')
             ->with($this->equalTo($sThumbsPath), $this->equalTo(octdec(self::THUMBNAIL_DIR_CHMOD)))
             ->will($this->returnValue(true));

        $this->oStandardMock->expects($this->once())
             ->method('copy')
             ->with($this->equalTo($sResource),
                    $this->equalTo($sThumbsPath . DIRECTORY_SEPARATOR . self::RESOURCE_FILE))
             ->will($this->returnValue(true));

        $this->oStandardMock->expects($this->once())
             ->method('chmod')
             ->with($this->equalTo($sThumbsPath . DIRECTORY_SEPARATOR . self::RESOURCE_FILE),
                    $this->equalTo(octdec(self::THUMBNAIL_FILE_CHMOD)))
             ->will($this->returnValue(true));

        //WHEN
        $this->oStorage->addResource(self::RESOURCE_PATH . DIRECTORY_SEPARATOR . self::RESOURCE_FILE);
        $this->oStorage->save();
    }

    public function tearDown()
    {
        new AM_Tools_Standard();
    }
}
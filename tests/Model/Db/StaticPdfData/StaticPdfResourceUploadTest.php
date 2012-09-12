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

class StaticPdfResourceUploadTest extends AM_Test_PHPUnit_DatabaseTestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject **/
    protected $_oStandardMock    = null;
    /** @var PHPUnit_Framework_MockObject_MockObject * */
    protected $_oUploaderMock    = null;
    protected $_oThumbnailerMock = null;
    protected $_oStaticPdf       = null;

    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'StaticPdfUploadResourceTest.yml';
    }

    public function setUp()
    {
        parent::setUp();

        $this->_oStaticPdf       = AM_Model_Db_Table_Abstract::factory('static_pdf')->findOneBy(array('id' => 1));
        $this->_oStandardMock    = $this->getMock('AM_Tools_Standard', array('is_dir', 'mkdir'));
        $this->_oUploaderMock    = $this->getMock('AM_Handler_Upload', array('isValid', 'isUploaded', 'getFileInfo', 'receive'));
        $this->_oThumbnailerMock = $this->getMock('AM_Handler_Thumbnail', array('addSourceFile', 'loadAllPresets', 'createThumbnails', 'getSources'));
        AM_Handler_Locator::getInstance()->setHandler('thumbnail', $this->_oThumbnailerMock);
    }

    public function testShouldUploadResource()
    {
        //GIVEN
        $oResource = new AM_Model_Db_StaticPdf_Data_Resource($this->_oStaticPdf);
        $oResource->setUploader($this->_oUploaderMock);
        $sResourceDir = $oResource->getResourceDir();

        //THEN
        $this->_oStandardMock->expects($this->once())
             ->method('is_dir')
             ->with($this->equalTo($sResourceDir))
             ->will($this->returnValue(false));

        $this->_oStandardMock->expects($this->once())
             ->method('mkdir')
             ->with($this->equalTo($sResourceDir),  $this->equalTo(0777), $this->equalTo(true))
             ->will($this->returnValue(true));

        $this->_oUploaderMock->expects($this->once())
             ->method('isUploaded')
             ->will($this->returnValue(true));

        $this->_oUploaderMock->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->_oUploaderMock->expects($this->once())
             ->method('getFileInfo')
             ->will($this->returnValue(array('pdf-file'=>array('name'=>'test.pdf'))));

        $this->_oUploaderMock->expects($this->once())
             ->method('receive')
             ->will($this->returnValue(true));

        $this->_oThumbnailerMock->expects($this->any())
                ->method('addSourceFile')
                ->will($this->returnValue($this->_oThumbnailerMock));

        $this->_oThumbnailerMock->expects($this->any())
                ->method('loadAllPresets')
                ->will($this->returnValue($this->_oThumbnailerMock));

        $this->_oThumbnailerMock->expects($this->any())
                ->method('getSources')
                ->will($this->returnValue(array()));

        //WHEN
        $oResource->upload();

        //THEN
        $this->assertEquals('test.pdf', $oResource->getResourceDbBaseName());
    }
}

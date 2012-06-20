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
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

class StaticPdfResourceUploadTest extends AM_Test_PHPUnit_DatabaseTestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject **/
    protected $_standardMock = null;
    /** @var PHPUnit_Framework_MockObject_MockObject **/
    protected $_uploaderMock = null;

    public function getDataSet()
    {
        $tableNames = array('static_pdf');
        $dataSet = $this->getConnection()->createDataSet($tableNames);
        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $this->pdf = new AM_Model_Db_StaticPdf();

        $this->pdf->setFromArray(array('id'     => 1,
                                       'issue'  => 1,
                                       'name'   => ''
                                     ));
        $this->pdf->save();

        $this->_standardMock = $this->getMock('AM_Tools_Standard', array('is_dir', 'mkdir'));
        $this->_uploaderMock = $this->getMock('AM_Handler_Upload', array('isValid', 'isUploaded', 'getFileInfo', 'receive'));
    }

    public function testShouldUploadResource()
    {
        //GIVEN
        $thumbnailerMock = $this->getMock('AM_Handler_Thumbnail', array('addSourceFile', 'loadAllPresets', 'createThumbnails', 'getSources'));
        AM_Handler_Locator::getInstance()->setHandler('thumbnail', $thumbnailerMock);

        $resource    = new AM_Model_Db_StaticPdf_Data_Resource($this->pdf);
        $resource->setUploader($this->_uploaderMock);
        $resourceDir = $resource->getResourceDir();

        //THEN
        $this->_standardMock->expects($this->once())
             ->method('is_dir')
             ->with($this->equalTo($resourceDir))
             ->will($this->returnValue(false));

        $this->_standardMock->expects($this->once())
             ->method('mkdir')
             ->with($this->equalTo($resourceDir),  $this->equalTo(0777), $this->equalTo(true))
             ->will($this->returnValue(true));

        $this->_uploaderMock->expects($this->once())
             ->method('isUploaded')
             ->will($this->returnValue(true));

        $this->_uploaderMock->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $this->_uploaderMock->expects($this->once())
             ->method('getFileInfo')
             ->will($this->returnValue(array('pdf-file'=>array('name'=>'test.pdf'))));

        $this->_uploaderMock->expects($this->once())
             ->method('receive')
             ->will($this->returnValue(true));

        $thumbnailerMock->expects($this->any())
                ->method('addSourceFile')
                ->will($this->returnValue($thumbnailerMock));

        $thumbnailerMock->expects($this->any())
                ->method('loadAllPresets')
                ->will($this->returnValue($thumbnailerMock));

        $thumbnailerMock->expects($this->any())
                ->method('getSources')
                ->will($this->returnValue(array()));

        //WHEN
        $resource->upload();

        //THEN
        $this->assertEquals('test.pdf', $resource->getResourceDbBaseName());
    }
}

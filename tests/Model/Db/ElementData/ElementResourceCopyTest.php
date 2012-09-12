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

class ElementResourceCopyTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'ElementResourceCopyTest.yml';
    }

    public function testShouldCopyResource()
    {
        //GIVEN
        $oThumbnailerMock = $this->getMock('AM_Handler_Thumbnail', array('addSourceFile', 'loadAllPresets', 'createThumbnails', 'getSources'));
        AM_Handler_Locator::getInstance()->setHandler('thumbnail', $oThumbnailerMock);
        $oStandardMock = $this->getMock('AM_Tools_Standard', array('is_dir', 'mkdir', 'copy'));

        $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', 1);
        $oResource = new AM_Model_Db_Element_Data_MockResource($oElement);
        $oResource->addAdditionalResourceKey('additional_key');
        //Emulating the element copying
        $oElement->id = 2;

        //The current path of the resource
        $sOldDir = AM_Tools::getContentPath(AM_Model_Db_Element_Data_Resource::TYPE, 1);
        //The path of the resource after copying
        $sNewDir = AM_Tools::getContentPath(AM_Model_Db_Element_Data_Resource::TYPE, 2);

        //THEN
        //Checking all file operations
        $oStandardMock->expects($this->at(0))
             ->method('is_dir')
             ->with($this->equalTo($sOldDir))
             ->will($this->returnValue(true));

        $oStandardMock->expects($this->at(1))
             ->method('is_dir')
             ->with($this->equalTo($sNewDir))
             ->will($this->returnValue(false));

        $oStandardMock->expects($this->once())
             ->method('mkdir')
             ->with($this->equalTo($sNewDir),  $this->equalTo(0777), $this->equalTo(true))
             ->will($this->returnValue(true));

        $sNewResourcePath = $sNewDir . DIRECTORY_SEPARATOR . 'resource.png';
        $sOldResourcePath = $sOldDir . DIRECTORY_SEPARATOR . 'resource.png';

        $sNewAdditionalResourcePath = $sNewDir . DIRECTORY_SEPARATOR . 'additional_key.png';
        $sOldAdditionalResourcePath = $sOldDir . DIRECTORY_SEPARATOR . 'additional_key.png';

        $oStandardMock->expects($this->at(3))
             ->method('copy')
             ->with($this->equalTo($sOldResourcePath),
                    $this->equalTo($sNewResourcePath))
             ->will($this->returnValue(true));

        $oStandardMock->expects($this->at(4))
             ->method('copy')
             ->with($this->equalTo($sOldAdditionalResourcePath),
                    $this->equalTo($sNewAdditionalResourcePath))
             ->will($this->returnValue(true));

        //Checking the init of the resizing operation
        $oThumbnailerMock->expects($this->any())
                ->method('addSourceFile')
                ->will($this->returnValue($oThumbnailerMock));

        $oThumbnailerMock->expects($this->any())
                ->method('loadAllPresets')
                ->will($this->returnValue($oThumbnailerMock));

        $oResourceMock = new AM_Resource_Concrete_Mock($sNewResourcePath);
        $oThumbnailerMock->expects($this->at(3))
                ->method('getSources')
                ->will($this->returnValue(array($oResourceMock)));

        $oAdditionalResourceMock = new AM_Resource_Concrete_Mock($sNewAdditionalResourcePath);
        $oThumbnailerMock->expects($this->at(7))
                ->method('getSources')
                ->will($this->returnValue(array($oAdditionalResourceMock)));

        //WHEN
        $oResource->copy();

        //THEN
        $oGivenDataSet    = $this->getConnection()->createQueryTable('element_data', 'SELECT id_element, key_name, value FROM element_data ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/ElementResourceCopyTest.xml')
                              ->getTable('element_data');

        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);

        //Checking the background tasks
        $oTasks = AM_Model_Db_Table_Abstract::factory('task')->findAllBy(array('status' => AM_Task_Worker_Abstract::STATUS_NEW));
        $this->assertEquals(2, $oTasks->count(), 'Precess should init 2 tasks');

        $oTaskForResource = $oTasks[0];
        $aExpectedParams = array('resource' => $sNewResourcePath, 'image_type' => 'png', 'zooming' => 0, 'resource_type' => 'element-vertical');
        $this->assertEquals($aExpectedParams, $oTaskForResource->getOptions());

        $oTaskForAdditionalResource = $oTasks[1];
        $aExpectedParams = array('resource' => $sNewAdditionalResourcePath, 'image_type' => 'png', 'zooming' => 0, 'resource_type' => 'element-vertical');
        $this->assertEquals($aExpectedParams, $oTaskForAdditionalResource->getOptions());
    }
}

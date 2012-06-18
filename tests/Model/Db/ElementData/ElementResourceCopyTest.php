<?php
class ElementResourceCopyTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject $standardMock **/
    var $standardMock = null;

    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'copy.yml';
    }

    public function testShouldCopyResource()
    {
        //GIVEN
        $thumbnailerMock = $this->getMock('AM_Handler_Thumbnail', array('addSourceFile', 'loadAllPresets', 'createThumbnails', 'getSources'));
        AM_Handler_Locator::getInstance()->setHandler('thumbnail', $thumbnailerMock);
        $this->standardMock = $this->getMock('AM_Tools_Standard', array('is_dir', 'mkdir', 'copy'));

        $element = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', 1);
        $resource = new AM_Model_Db_Element_Data_MockResource($element);
        $resource->addAdditionalResourceKey('additional_key');
        $element->id = 2;

        $oldDir = AM_Tools::getContentPath(AM_Model_Db_Element_Data_Resource::TYPE, 1);
        $newDir = AM_Tools::getContentPath(AM_Model_Db_Element_Data_Resource::TYPE, 2);

        //THEN
        $this->standardMock->expects($this->at(0))
             ->method('is_dir')
             ->with($this->equalTo($oldDir))
             ->will($this->returnValue(true));

        $this->standardMock->expects($this->at(1))
             ->method('is_dir')
             ->with($this->equalTo($newDir))
             ->will($this->returnValue(false));

        $this->standardMock->expects($this->once())
             ->method('mkdir')
             ->with($this->equalTo($newDir),  $this->equalTo(0777), $this->equalTo(true))
             ->will($this->returnValue(true));

        $this->standardMock->expects($this->at(3))
             ->method('copy')
             ->with($this->equalTo($oldDir . DIRECTORY_SEPARATOR . "resource.png"),
                    $this->equalTo($newDir . DIRECTORY_SEPARATOR . "resource.png"))
             ->will($this->returnValue(true));

        $this->standardMock->expects($this->at(4))
             ->method('copy')
             ->with($this->equalTo($oldDir . DIRECTORY_SEPARATOR . "additional_key.png"),
                    $this->equalTo($newDir . DIRECTORY_SEPARATOR . "additional_key.png"))
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
        $resource->copy();

        //THEN
        $queryTable    = $this->getConnection()->createQueryTable("element_data", "SELECT id_element, key_name, value FROM element_data ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy.xml")
                              ->getTable("element_data");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

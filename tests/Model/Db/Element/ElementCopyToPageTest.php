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

class ElementCopyToPageTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'copy_to_page.yml';
    }

    public function testShouldCopyToPage()
    {
        //GIVEN
        $this->element = AM_Model_Db_Table_Abstract::factory('element')->findOneBy('id', 1);
        $this->resourceMock = $this->getMock('AM_Model_Db_Element_Data_Resource', array('copy'), array($this->element));
        $this->element->setResources($this->resourceMock);

        $pageData = array("id" => 2, "title" => "test_page");
        $page = new AM_Model_Db_Page();
        $page->setFromArray($pageData);

        //THEN
        $this->resourceMock->expects($this->once())
            ->method('copy');

        //WHEN
        $this->element->copyToPage($page);
        $this->element->refresh();

        //THEN
        $this->assertEquals(2, $this->element->page, "Page id should change");

        $queryTable    = $this->getConnection()->createQueryTable("element", "SELECT id, page FROM element ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/copy.xml")
                              ->getTable("element");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

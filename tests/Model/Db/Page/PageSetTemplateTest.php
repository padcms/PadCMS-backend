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

class PageSetTemplateTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'set_template.yml';
    }

    public function testShouldSetTemplate()
    {
        //GIVEN
        $page        = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('id' => 1));
        /* @var $page AM_Model_Db_Page */
        $newTemplate = AM_Model_Db_Table_Abstract::factory('template')->findOneBy(array('id' => 2));
        /* @var $newTemplate AM_Model_Db_Template */

        //WHEN
        $page->setTemplate($newTemplate);

        //THEN
        //Checking page table
        $queryTable    = $this->getConnection()->createQueryTable('page', 'SELECT id, template FROM page ORDER BY id');
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/setTemplate.xml')
                              ->getTable('page');
        $this->assertTablesEqual($expectedTable, $queryTable);

        //Checking element table
        $queryTable    = $this->getConnection()->createQueryTable('element', 'SELECT id, page, field FROM element ORDER BY id');
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/setTemplate.xml')
                              ->getTable('element');
        $this->assertTablesEqual($expectedTable, $queryTable);

        //Check page background
        $this->assertEquals(0, AM_Model_Db_Table_Abstract::factory('page_background')->fetchAll()->count(), 'Error while assering, that "page_background" table is empty');
    }
}

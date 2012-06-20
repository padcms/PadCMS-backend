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

class StaticPdfUploadResourceTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject **/
    protected $_resourceMock = null;
    /** @var AM_Model_Db_StaticPdf **/
    protected $_staticPdf    = null;

    public function getDataSet()
    {
        $tableNames = array('static_pdf');
        $dataSet = $this->getConnection()->createDataSet($tableNames);

        return $dataSet;
    }

    public function setUp()
    {
        parent::setUp();

        $staticPdfData = array("id" => 1, "issue" => 1, "name" => "");
        $this->_staticPdf = new AM_Model_Db_StaticPdf();
        $this->_staticPdf->setFromArray($staticPdfData);
        $this->_staticPdf->save();

        $this->_resourceMock = $this->getMock('AM_Model_Db_StaticPdf_Data_Resource', array('upload', 'getResourceDbBaseName'), array($this->_staticPdf));
        $this->_staticPdf->setResources($this->_resourceMock);
    }

    public function testShouldUploadResource()
    {
        //GIVEN
        $this->_resourceMock->expects($this->once())
             ->method('upload');

        $this->_resourceMock->expects($this->once())
             ->method('getResourceDbBaseName')
             ->will($this->returnValue('static.pdf'));

        //WHEN
        $this->_staticPdf->uploadResource();
        $this->_staticPdf->refresh();

        //THEN
        $queryTable    = $this->getConnection()->createQueryTable("static_pdf", "SELECT id, issue, name FROM static_pdf ORDER BY id");
        $expectedTable = $this->createFlatXMLDataSet(dirname(__FILE__) . "/_dataset/upload.xml")
                              ->getTable("static_pdf");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }
}

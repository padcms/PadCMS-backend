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

class ApiClientGetResolutionsTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _mockThumbnailer()
    {
        $aConfig = array();
        $aConfig['common']['resource'][AM_Model_Db_Element_Data_Abstract::TYPE . '-vertical']         = array('none', '1024-768');
        $aConfig['common']['resource'][AM_Model_Db_Element_Data_Abstract::TYPE . '-horizontal']       = array('none', '768-1024');
        $aConfig['common']['resource'][AM_Model_Db_Term_Data_Abstract::TYPE]                          = array('none', '1024-768');
        $aConfig['common']['resource'][AM_Model_Db_StaticPdf_Data_Abstract::TYPE_CACHE]               = array('none', '1024-768');
        $aConfig['common']['resource'][AM_Model_Db_IssueHelpPage_Data_Abstract::TYPE . '-vertical']   = array('none', '1024-768');
        $aConfig['common']['resource'][AM_Model_Db_IssueHelpPage_Data_Abstract::TYPE . '-horizontal'] = array('none', '1024-768');

        $oThumbnailer = new AM_Handler_Thumbnail();
        $oThumbnailer->setConfig(new Zend_Config($aConfig, true));
        AM_Handler_Locator::getInstance()->setHandler('thumbnail', $oThumbnailer);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_mockThumbnailer();
    }

    public function testShouldReturnResolutionsList()
    {
        //GIVEN
        $oApiClient = new AM_Api_Client();

        //WHEN
        $aResult = $oApiClient->getResolutions();

        //THEN
        $aExpectedData = array('page-horizontal'      => array('1024-768'),
                               'menu'                 => array('1024-768'),
                               'element-vertical'     => array('1024-768'),
                               'element-horizontal'   => array('768-1024'),
                               'help-page-vertical'   => array('1024-768'),
                               'help-page-horizontal' => array('1024-768'),);

        $this->assertEquals($aExpectedData, $aResult);
    }

    protected function tearDown()
    {
        parent::tearDown();
        AM_Handler_Locator::getInstance()->setHandler('thumbnail', 'AM_Handler_Thumbnail');
    }
}
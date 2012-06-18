<?php
/**
 * @author vl4dimir
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
<?php

class ApiApnsSetDeviceTokenTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'ApiApnsSetDeviceTokenTest.yml';
    }

    public function testShouldSaveDeviceToken()
    {
        //GIVEN
        $sUdid          = 'test_udid';
        $iApplicationId = 11;
        $sToken         = 'test_token';

        $oApi = new AM_Api_Apns();

        //WHEN
        $aResult = $oApi->setDeviceToken($sUdid, $iApplicationId, $sToken);

        //THEN
        $this->assertEquals(array('code' => AM_Api_Apns::RESULT_SUCCESS), $aResult);

        $oGivenDataSet    = $this->getConnection()->createQueryTable('device_token', 'SELECT id, udid, token, application_id FROM device_token ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/ApiApnsSetDeviceTokenTest.xml')
                              ->getTable('device_token');
        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }

    public function testShouldntSaveDeviceTokenTwice()
    {
        //GIVEN
        $sUdid          = 'test_udid';
        $iApplicationId = 11;
        $sToken         = 'test_token';

        $oApi = new AM_Api_Apns();

        //WHEN
        $aResult = $oApi->setDeviceToken($sUdid, $iApplicationId, $sToken);
        $aResult = $oApi->setDeviceToken($sUdid, $iApplicationId, $sToken);

        //THEN
        $this->assertEquals(array('code' => AM_Api_Apns::RESULT_RECORD_EXISTS), $aResult);

        $oGivenDataSet    = $this->getConnection()->createQueryTable('device_token', 'SELECT id, udid, token, application_id FROM device_token ORDER BY id');
        $oExpectedDataSet = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_dataset/ApiApnsSetDeviceTokenTest.xml')
                              ->getTable('device_token');
        $this->assertTablesEqual($oExpectedDataSet, $oGivenDataSet);
    }
}
<?php
/**
 * @author vl4dimir
 */
class ApiUserLoginTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'ApiUserLoginTest.yml';
    }

    protected function setUp()
    {
        parent::setUp();
    }

    public function testShouldAuthExistingUserAndReturnSessionId()
    {
        //GIVEN
        Zend_Session::$_unitTestEnabled = true;
        $oApiUser = new AM_Api_User();

        //WHEN
        $aResult = $oApiUser->login('john', 'password');

        //THEN
        $oExpectedUserObject = new stdClass();
        $oExpectedUserObject->first_name = 'John';
        $oExpectedUserObject->last_name  = 'Doe';
        $oExpectedUserObject->login      = 'john';
        $oExpectedUserObject->email      = 'john@mail.com';
        $oExpectedUserObject->id         = 1;
        $oExpectedUserObject->client     = 1;
        $oExpectedUserObject->is_admin   = 0;

        $aExpectedResult = array('code'      => 1,
                                 'sessionId' => Zend_Session::getId(),
                                 'userInfo'  => $oExpectedUserObject);
        $this->assertEquals($aExpectedResult, $aResult);
    }

    public function testShouldNotAuthNoExistingUserAndReturnErrorCode()
    {
        //GIVEN
        Zend_Session::$_unitTestEnabled = true;
        $oApiUser = new AM_Api_User();

        //WHEN
        $aResult = $oApiUser->login('no-existing-user', 'password');

        //THEN
        $aExpectedResult = array('code' => Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                                 'messages' => array('A record with the supplied identity could not be found.'));
        $this->assertEquals($aExpectedResult, $aResult);
    }
}
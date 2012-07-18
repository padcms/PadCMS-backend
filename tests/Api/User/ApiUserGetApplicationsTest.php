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

class ApiUserGetApplicationsTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'ApiUserGetApplicationsTest.yml';
    }

    public function testShouldReturnListOfApplicationsForLoggedInUser()
    {
        //GIVEN
        date_default_timezone_set('Etc/GMT');
        Zend_Session::$_unitTestEnabled = true;

        $oStorageMock = $this->getMock('Zend_Auth_Storage_Session');
        Zend_Auth::getInstance()->setStorage($oStorageMock);

        $oExpectedUserObject = new stdClass();
        $oExpectedUserObject->first_name = 'John';
        $oExpectedUserObject->last_name  = 'Doe';
        $oExpectedUserObject->login      = 'john';
        $oExpectedUserObject->email      = 'john@mail.com';
        $oExpectedUserObject->id         = 1;
        $oExpectedUserObject->client     = 1;
        $oExpectedUserObject->is_admin   = 0;

        $oApiUser = new AM_Api_User();

        //THEN
        $oStorageMock->expects($this->any())
                ->method('read')
                ->will($this->returnValue($oExpectedUserObject));

        //WHEN
        $aResult = $oApiUser->getApplications('vp23rk326iem65udi5q38ob0o7');

        //THEN
        $aExpectedResult = array(
            'code'         => AM_Api_User::RESULT_SUCCESS,
            'applications' => array(
                1 => array(
                    'application_id'                       => 1,
                    'application_title'                    => 'Title',
                    'application_description'              => 'Description',
                    'application_product_id'               => 'com.padcms.application_1',
                    'application_notification_email'       => 'Email message',
                    'application_notification_email_title' => 'Email title',
                    'application_notification_twitter'     => 'Twitter message',
                    'application_notification_facebook'    => 'Facebook message',
                    'application_preview'                  => 2,
                    'issues' => array (
                        1 => array(
                            'issue_id'              => 1,
                            'issue_title'           => 'Title',
                            'issue_number'          => 1,
                            'issue_state'           => 'work-in-progress',
                            'issue_product_id'      => 'com.padcms.issue_1',
                            'revisions' => array(
                                1 => array(
                                    'revision_id'               => 1,
                                    'revision_title'            => 'Title',
                                    'revision_state'            => 'work-in-progress',
                                    'revision_cover_image_list' => '/resources/export-cover-vertical/element/00/00/00/01/resource.png?',
                                    'revision_video'            => '/resources/none/element/00/00/00/02/resource.mp4',
                                    'revision_color'            => 'd9411a',
                                    'revision_horizontal_mode'  => '2pages',
                                    'revision_orientation'      => 'vertical',
                                    'help_pages'                => array(AM_Model_Db_IssueHelpPage::TYPE_HORIZONTAL => '/issue-help-page-horizontal/00/00/00/01/horizontal.png',
                                                                         AM_Model_Db_IssueHelpPage::TYPE_VERTICAL   => '/issue-help-page-vertical/00/00/00/01/vertical.png'),
                                    'revision_created'          => '2012-05-04T15:47:03+00:00'
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->assertEquals($aExpectedResult, $aResult);
    }

    protected function tearDown()
    {
        parent::tearDown();
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session());
    }
}
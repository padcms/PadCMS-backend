<?php
/**
 * @file
 * AM_Api_User class definition.
 *
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

/**
 * This class is responsible for user authentication
 * @ingroup AM_Api
 */
class AM_Api_User extends AM_Api
{
    const RESULT_WRONG_SESSION_ID = -1;
    const RESULT_SUCCESS          = 1;
    /**
     * @param string $sUserLogin
     * @param string $sUserPassword
     * @return array
     */
    public function login($sUserLogin, $sUserPassword)
    {
        $oAuth = Zend_Auth::getInstance();

        $oAuthAdapter = new Zend_Auth_Adapter_DbTable();
        $oAuthAdapter->setTableName('user')
                ->setIdentityColumn('login')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('MD5(?)');

        $oAuthAdapter->setIdentity($sUserLogin)
                ->setCredential($sUserPassword);

        $oSelect = $oAuthAdapter->getDbSelect();
        $oSelect->where('user.deleted = ?', 'no');

        $oResult = $oAuth->authenticate($oAuthAdapter);

        $aResult = array('code' => $oResult->getCode());

        if ($oResult->isValid()) {
            $oUser = $oAuthAdapter->getResultRowObject(array('id', 'client', 'first_name', 'last_name', 'login', 'email', 'is_admin'));
            $oAuth->getStorage()->write($oUser);

            $aResult['sessionId'] = Zend_Session::getId();
            $aResult['userInfo']  = $oAuth->getIdentity();

            return $aResult;
        }

        $aResult['messages'] = $oResult->getMessages();

        return $aResult;
    }

    /**
     * @param string $sSessionId
     * @param string $sPlatform
     * @return array
     */
    public function getApplications($sSessionId, $sPlatform = self::PLATFORM_IOS)
    {
        Zend_Session::setId((string) $sSessionId);

        $oAuth = Zend_Auth::getInstance();

        if (!$oAuth->hasIdentity()) {
            $aResult = array('code' => self::RESULT_WRONG_SESSION_ID, 'messages' => array('Failure due to incorrect session id'));

            return $aResult;
        }

        $sPlatform = trim($sPlatform);

        if (!in_array($sPlatform, $this->_aValidPlatforms)) {
            $sPlatform = self::PLATFORM_IOS;
        }

        $aResult = array('code' => self::RESULT_SUCCESS, 'applications' => array());

        $oUser = $oAuth->getIdentity();

        $aCriteria = array('deleted' => 'no');

        if (!$oUser->is_admin) {
            $aCriteria['client'] = $oUser->client;
        }

        $oApplications = AM_Model_Db_Table_Abstract::factory('application')->findAllBy($aCriteria);

        foreach ($oApplications as $oApplication){
            $aApplication = array(
                'application_id'                       => $oApplication->id,
                'application_title'                    => $oApplication->title,
                'application_description'              => $oApplication->description,
                'application_product_id'               => $oApplication->product_id,
                'application_notification_email'       => $oApplication->{'nm_email_' . $sPlatform},
                'application_notification_email_title' => $oApplication->{'nt_email_' . $sPlatform},
                'application_notification_twitter'     => $oApplication->{'nm_twitter_' . $sPlatform},
                'application_notification_facebook'    => $oApplication->{'nm_fbook_' . $sPlatform},
                'issues'                               => array()
            );

            $oIssues = AM_Model_Db_Table_Abstract::factory('issue')->findAllBy(array('application' => $oApplication->id, 'deleted' => 'no'));

            foreach ($oIssues as $oIssue) {
                $aIssue = array(
                    'issue_id'              => $oIssue->id,
                    'issue_title'           => $oIssue->title,
                    'issue_number'          => $oIssue->number,
                    'issue_state'           => AM_Model_Db_State::stateToText($oIssue->state),
                    'issue_product_id'      => $oIssue->product_id,
                    'revisions'             => array()
                );

                //Prepearing help pages
                $oHelpPages = AM_Model_Db_Table_Abstract::factory('issue_help_page')->findAllBy(array('id_issue' => $oIssue->id));

                $oRevisions = AM_Model_Db_Table_Abstract::factory('revision')->findAllBy(array('issue' => $oIssue->id, 'deleted' => 'no'));

                foreach ($oRevisions as $oRevision) {
                    $aRevision = array(
                        'revision_id'               => $oRevision->id,
                        'revision_title'            => $oRevision->title,
                        'revision_state'            => AM_Model_Db_State::stateToText($oRevision->state),
                        'revision_cover_image_list' => '',
                        'revision_video'            => '',
                        'revision_created'          => null,
                        'revision_color'            => $oIssue->issue_color,
                        'revision_horizontal_mode'  => $oIssue->static_pdf_mode,
                        'revision_orientation'      => $oIssue->orientation,
                        'help_pages'                => array(AM_Model_Db_IssueHelpPage::TYPE_HORIZONTAL => '', AM_Model_Db_IssueHelpPage::TYPE_VERTICAL => ''),
                    );

                    foreach ($oHelpPages as $oHelpPage) {
                        /* @var $oHelpPage AM_Model_Db_IssueHelpPage */
                        $aRevision['help_pages'][$oHelpPage->type] = (string) $oHelpPage->getResource()->getResourcePathForExport();
                    }

                    //Revision creation date
                    $oDate = new Zend_Date($oRevision->created);
                    $aRevision['revision_created'] = $oDate->toString(Zend_Date::ISO_8601);

                    $oPageCover = AM_Model_Db_Table_Abstract::factory('page')->findOneBy(array('revision' => $oRevision->id,
                                                                              'template' => AM_Model_Db_Template::TPL_COVER_PAGE,
                                                                              'deleted'  => 'no'));
                    /* @var $oPageCover AM_Model_Db_Page */
                    if (!is_null($oPageCover)) {
                        $aRevision['revision_cover_image_list'] = (string) $oPageCover->getPageCoverUri();
                        $aRevision['revision_video']            = (string) $oPageCover->getStartVideoUri();
                    }

                    $aIssue['revisions'][$oRevision->id] = $aRevision;
                }

                $aApplication['issues'][$oIssue->id] = $aIssue;
            }

            $aResult['applications'][$oApplication->id] = $aApplication;
        }

        return $aResult;
    }
}

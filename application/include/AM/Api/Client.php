<?php
/**
 * @file
 * AM_Api_Client class definition.
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
 * This class is responsible for sending information about client's issues, applications, revisions
 * @ingroup AM_Api
 */
class AM_Api_Client extends AM_Api
{
    const RESULT_FAIL    = -1;
    const RESULT_SUCCESS = 1;

    /**
     * Get client application/issues/revision tree
     *
     * @param int $iApplicationId
     * @param string $sUdid
     * @return array
     */
    public function getIssues($iApplicationId, $sUdid = null, $sPlatform = self::PLATFORM_IOS)
    {
        $iApplicationId = intval($iApplicationId);

        if ($iApplicationId <= 0) {
            throw new AM_Api_Client_Exception(sprintf('Invalid application id "%d"', $iApplicationId));
        }

        $sPlatform = trim($sPlatform);

        if (!in_array($sPlatform, $this->_aValidPlatforms)) {
            $sPlatform = self::PLATFORM_IOS;
        }

        if (!is_null($sUdid)) {
            $sUdid = trim($sUdid);
        }
        //Does udid belong to admin user
        $bIsUdidUserAdmin = false;
        $oDevice          = null;

        if (!empty($sUdid)) {
            $oDevice = AM_Model_Db_Table_Abstract::factory('device')->findOneBy(array('identifer' => $sUdid));

            if (!is_null($oDevice)) {
                $oUser = $oDevice->getUser();
                if (!is_null($oUser)) {
                    $bIsUdidUserAdmin = (bool) $oUser->is_admin;
                }
            }
        }

        $aResult = array('code' => self::RESULT_SUCCESS, 'applications' => array());

        $oApplications = AM_Model_Db_Table_Abstract::factory('application')->findAllBy(array('id' => $iApplicationId, 'deleted' => 'no'));

        foreach ($oApplications as $oApplication) {

            if (!is_null($oDevice)) {
                if (!is_null($oDevice->getUser()) && $oApplication->client == $oDevice->getUser()->client) {
                    $bIsUdidUserAdmin = true;
                }
            }

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

            //Checking subscripton
            $oSubscription = null;
            if (!is_null($oDevice)) {
                $oSubscription = AM_Model_Db_Table_Abstract::factory('purchase')
                        ->findOneBy(array('device_id' => $oDevice->id, 'product_id' => $oApplication->product_id, 'deleted' => 'no'));
                /* @var $oSubscription AM_Model_Db_Purchase */

                if (!is_null($oSubscription)) {
                    if ($oSubscription->isExpired()) {
                            $oSubscription = null;
                    }
                }
            }

            //Looking for the issues
            $aCriteria = array('application' => $oApplication->id, 'deleted' => 'no');

            if (!$bIsUdidUserAdmin) {
                $aCriteria['state'] = AM_Model_Db_State::STATE_PUBLISHED;
            }

            $oIssues = AM_Model_Db_Table_Abstract::factory('issue')->findAllBy($aCriteria);
            foreach ($oIssues as $oIssue) {
                $aIssue = array(
                    'issue_id'              => $oIssue->id,
                    'issue_title'           => $oIssue->title,
                    'issue_number'          => $oIssue->number,
                    'issue_state'           => AM_Model_Db_State::stateToText($oIssue->state),
                    'issue_product_id'      => $oIssue->product_id,
                    'paid'                  => false,
                    'revisions'             => array()
                );

                //Prepearing help pages
                $oHelpPages = AM_Model_Db_Table_Abstract::factory('issue_help_page')->findAllBy(array('id_issue' => $oIssue->id));

                //Checking the payment
                if (!is_null($oDevice)) {
                    if (!is_null($oSubscription)) {
                        $aIssue['paid']              = true;
                        $aIssue['subscription_type'] = $oSubscription->subscription_type;
                    } else {
                        $oPurchase = AM_Model_Db_Table_Abstract::factory('purchase')
                                ->findOneBy(array('device_id' => $oDevice->id, 'product_id' => $oIssue->product_id, 'deleted' => 'no'));

                        if (!is_null($oPurchase)) {
                            $aIssue['paid'] = true;
                        }
                    }
                }

                //Looking for the revisions
                $aCriteria = array('issue' => $oIssue->id, 'deleted' => 'no');

                if (!$bIsUdidUserAdmin) {
                    $aCriteria['state'] = AM_Model_Db_State::STATE_PUBLISHED;
                }

                $oRevisions = AM_Model_Db_Table_Abstract::factory('revision')->findAllBy($aCriteria);

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

    /**
     * Get resolutions list
     *
     * @return array
     * @throws AM_Api_Client_Exception
     */
    public function getResolutions()
    {
        $mResponse = array();

        $oThumbnailer = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
        /* @var $oThumbnailer AM_Handler_Thumbnail */

        $mResponse['page-horizontal']      = $oThumbnailer->getResolutions(AM_Model_Db_StaticPdf_Data_Abstract::TYPE_CACHE);
        $mResponse['menu']                 = $oThumbnailer->getResolutions(AM_Model_Db_Term_Data_Abstract::TYPE);
        $mResponse['element-vertical']     = $oThumbnailer->getResolutions(AM_Model_Db_Element_Data_Abstract::TYPE . '-vertical');
        $mResponse['element-horizontal']   = $oThumbnailer->getResolutions(AM_Model_Db_Element_Data_Abstract::TYPE . '-horizontal');
        $mResponse['help-page-vertical']   = $oThumbnailer->getResolutions(AM_Model_Db_IssueHelpPage_Data_Abstract::TYPE . '-vertical');
        $mResponse['help-page-horizontal'] = $oThumbnailer->getResolutions(AM_Model_Db_IssueHelpPage_Data_Abstract::TYPE . '-horizontal');

        return $mResponse;
    }
}
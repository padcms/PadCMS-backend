<?php
/**
 * @file
 * AM_Component_Record_Database_Application class definition.
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
 * Application record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database_Application_Rue89we extends AM_Component_Record_Database_Application_Abstract
{

    /**
     *
     * @param AM_Controller_Action $oActionController
     * @param string $sName
     * @param int $iId
     * @param int $iClientId
     * @return void
     */
    public function  __construct(AM_Controller_Action $oActionController, $sName, $iId, $iClientId)
    {
        parent::__construct($oActionController, $sName, $iId, $iClientId);

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'preview',
                'Preview',
                array(array('numeric'))));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'description',
                'Description',
                array(array('require'))));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'welcome',
                'Welcome',
                array(array('maximum length', 350)),
                'welcome'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'product_id',
                'Product id',
                array(array('regexp', '/^[a-zA-Z0-9\._]+$/'))));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nm_twitter_ios',
                'Message'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nm_fbook_ios',
                'Message'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nt_email_ios',
                'Title'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nm_email_ios',
                'Message'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nm_twitter_android',
                'Message'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nm_fbook_android',
                'Message'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nt_email_android',
                'Title'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'nm_email_android',
                'Message'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'push_apple_enabled',
                'Enable'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'push_boxcar_enabled',
                'Enable'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'push_boxcar_provider_key',
                'Key'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'push_boxcar_provider_secret',
                'Secret'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'message_for_readers',
                'For our readers'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'share_url',
                'Share url'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'share_message',
                'Share message',
                array(array('maximum length', 200))));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'application_notification_google',
                'Notification Google'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'application_email',
                'Application Email'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'subscribe_title', 'Subscribe popup title', array(), 'subscribe_title'));

        $this->addControl(new Volcano_Component_Control_Database($oActionController,
                'subscribe_button', 'Subscribe button subtitle', array(), 'subscribe_button'));

        $this->addControl(new Volcano_Component_Control_Database_Static($oActionController,
                'updated',
                new Zend_Db_Expr('NOW()')));

        $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $iId);
        $sImageValue = !empty($oApplication->image) ? $oApplication->image : null;
        $this->addControl(new AM_Component_Control_Database_File($oActionController,
                'newsstand_cover_image', 'Cover image',  array(), 'newsstand_cover_image',
                AM_Tools::getContentPath(AM_Model_Db_Application::PRESET_APPLICATION_IMAGE)
                . DIRECTORY_SEPARATOR . '[ID]', TRUE, $sImageValue));

        $this->postInitialize();
    }

    public function show()
    {
        if (!$this->isSubmitted) {
            if (!$this->controls['preview']->getValue())
                $this->controls['preview']->setValue(0);
        }

        if ($this->primaryKeyValue) {
            $oApplication = AM_Model_Db_Table_Abstract::factory('application')->findOneBy('id', $this->primaryKeyValue);

            if ($oApplication->newsstand_cover_image) {
                $sIssueImageUri = AM_Tools::getImageUrl('270-150', AM_Model_Db_Application::PRESET_APPLICATION_IMAGE, $this->primaryKeyValue, $oApplication->newsstand_cover_image, 'png');
                if (!empty($oApplication->updated)) {
                    $sIssueImageUri .= '?' . strtotime($oApplication->updated);
                }
            }
        }

        $aRecord = array(
            'imageUri' => isset($sIssueImageUri) ? $sIssueImageUri : null,
        );

        if (isset($this->view->{$this->getName()})) {
            $aRecord = array_merge($aRecord, $this->view->{$this->getName()});
        }

        $this->view->{$this->getName()} = $aRecord;

        parent::show();
    }

    protected function _preOperation()
    {
        $sDescription = $this->controls['description']->getValue();
        $this->controls['description']->setValue(AM_Tools::filter_xss($sDescription));

        $sProductId = $this->controls['product_id']->getValue();
        $this->controls['product_id']->setValue(AM_Tools::filter_xss($sProductId));
    }
}

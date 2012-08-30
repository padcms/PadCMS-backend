<?php
/**
 * @file
 * AM_Component_Record_Database_Issue class definition.
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
 * Issue record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database_Issue extends AM_Component_Record_Database
{
    const STATE_WORK_IN_PROGRESS = 1;

    /** @var array */
    protected static $_aValidStates = array(
        AM_Model_Db_State::STATE_WORK_IN_PROGRESS,
        AM_Model_Db_State::STATE_PUBLISHED,
        AM_Model_Db_State::STATE_ARCHIVED
    ); /**< @type array */

    /** @var array */
    protected static $_aValidOrientations = array(
        AM_Model_Db_Issue::ORIENTATION_VERTICAL   => AM_Model_Db_Issue::ORIENTATION_VERTICAL,
        AM_Model_Db_Issue::ORIENTATION_HORIZONTAL => AM_Model_Db_Issue::ORIENTATION_HORIZONTAL
    ); /**< @type array */

    public function  __construct(AM_Controller_Action $oActionController, $sName, $iIssueId, $iApplicationId)
    {
        $aUser = $oActionController->getUser();

        $this->applicationId = $iApplicationId;

        $aControls = array();

        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'title', 'Title', array(array('require')), 'title');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'number', 'Number', array(array('require')), 'number');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'product_id', 'Product Id');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'state', 'State', array(array('require')), 'state');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'type', 'Issue type', array(array('require')), 'type');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'orientation', 'Orientation', array(), 'orientation');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'pdf_type', 'Horizontal PDF', null, 'static_pdf_mode');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'issue_color', 'Issue color', array(array('color')));
        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'application', $iApplicationId);
        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'updated', new Zend_Db_Expr('NOW()'));

        if (!$iIssueId) {
            $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'user', $aUser['id']);
        }

        return parent::__construct($oActionController, $sName, $aControls, $oActionController->oDb, 'issue', 'id', $iIssueId);
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        if (!parent::validate()) {
            return false;
        }

        if (!is_numeric($this->controls['application']->getValue())) {
            $this->errors[] = $this->actionController->__('Application no defined');

            return false;
        }

        // Check orientation
        if (!$this->primaryKeyValue) {
            if (array_search($this->controls['orientation']->getValue(), self::$_aValidOrientations) === false) {
                $this->errors[] = $this->actionController->__('Not valid orientation type');

                return false;
            }
        }

        // Check state
        if (array_search($this->controls['state']->getValue(), self::$_aValidStates) === false) {
            $this->errors[] = $this->actionController->__('Not valid state');

            return false;
        }

        // Check number for uniqueness
        $oQuery = $this->db->select()
                ->from('issue', 'COUNT(*)')
                ->where('issue.number = ?', $this->controls['number']->getValue())
                ->where('issue.deleted = "no"')
                ->where('issue.application = ?', $this->controls['application']->getValue());

        if ($this->primaryKeyValue) {
            $oQuery->where('issue.id != ?', $this->primaryKeyValue);
        }

        if ($this->db->fetchOne($oQuery)) {
            $this->errors[] = $this->actionController->__('The number is not unique');

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function operation()
    {
        if (!$this->primaryKeyValue) {
            $this->controls['state']->setValue(AM_Model_Db_State::STATE_WORK_IN_PROGRESS);
        }

        return parent::operation();
    }

    /**
     * @return void
     */
    public function show()
    {
        if (!$this->controls['state']->getValue()) {
            $this->controls['state']->setValue(AM_Model_Db_State::STATE_WORK_IN_PROGRESS);
        }

        $aStates = null;
        $oQuery  = $this->db->select()->from('state', array('id', 'title'))
                ->where('id in (' . implode(',', self::$_aValidStates) . ')')
                ->order('id ASC');
        $aStates = $this->db->fetchPairs($oQuery);

        $iLastRevision = null;

        $oQuery = $this->db->select()
                ->from('revision', array('last_revision' => 'MAX(revision.id)'))
                ->where('revision.issue = ?', $this->primaryKeyValue);

        $iLastRevision = $this->db->fetchOne($oQuery);

        $aSimplePdf          = false;
        $aVerticalHelpPage   = false;
        $aHorizontalHelpPage = false;

        if ($this->primaryKeyValue) {
            $oQuery = $this->db->select()->from('issue', array('static_pdf_mode'))
                    ->where('id = ?', $this->primaryKeyValue);
            $sStaticPdfMode = $this->db->fetchOne($oQuery);

            $oQuery = $this->db->select()->from('static_pdf', array('id', 'name', 'issue', 'updated' => 'UNIX_TIMESTAMP(updated)'))
                    ->where('issue = ?', $this->primaryKeyValue)
                    ->order('weight ASC');

            $aPdfFiles = $this->db->fetchAll($oQuery);

            foreach ($aPdfFiles as &$aFile) {
                $aFile['smallUri'] = AM_Tools::getImageUrl(
                                'static-pdf-thumbnail',
                                'static-pdf',
                                $aFile['issue'],
                                $aFile['id'] . '.png') . '?' . strtotime($aFile['updated']);
                $aFile['name']      = $aFile['name'];
                $aFile['nameShort'] = $this->actionController->getHelper('String')->cut($aFile['name']);
                $aFile['bigUri']    = AM_Tools::getImageUrl(
                                '1024-768',
                                'static-pdf',
                                $aFile['issue'],
                                $aFile['id'] . '.png') . '?' . strtotime($aFile['updated']);
            }

            //Get simple PDF
            $oSimplePdf = AM_Model_Db_Table_Abstract::factory('issue_simple_pdf')->findOneBy('id_issue', $this->primaryKeyValue);
            if (!is_null($oSimplePdf)) {
                $aSimplePdf['smallUri']  = AM_Tools::getImageUrl(
                        AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE,
                        AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE,
                        $oSimplePdf->id_issue,
                        $oSimplePdf->id_issue . '.png') . '?' . strtotime($oSimplePdf->updated);
                $aSimplePdf['name']      = $oSimplePdf->name;
                $aSimplePdf['nameShort'] = $this->actionController->getHelper('String')->cut($oSimplePdf->name);
                $aSimplePdf['bigUri']    = AM_Tools::getImageUrl(
                        '768-1024',
                        AM_Model_Db_IssueSimplePdf_Data_Abstract::TYPE,
                        $oSimplePdf->id_issue,
                        $oSimplePdf->id_issue . '.png') . '?' . strtotime($oSimplePdf->updated);
            }

            //Get help pages
            $oVerticalHelpPage = AM_Model_Db_Table_Abstract::factory('issue_help_page')
                    ->findOneBy(array('id_issue' => $this->primaryKeyValue, 'type' => AM_Model_Db_IssueHelpPage::TYPE_VERTICAL));
            if (!is_null($oVerticalHelpPage)) {
                $aVerticalHelpPage['smallUri']  = AM_Tools::getImageUrl(
                        $oVerticalHelpPage->getThumbnailPresetType(),
                        $oVerticalHelpPage->getThumbnailPresetType(),
                        $oVerticalHelpPage->id_issue,
                        $oVerticalHelpPage->type . '.png') . '?' . strtotime($oVerticalHelpPage->updated);
                $aVerticalHelpPage['name']      = $oVerticalHelpPage->name;
                $aVerticalHelpPage['nameShort'] = $this->actionController->getHelper('String')->cut($oVerticalHelpPage->name, 12);
                $aVerticalHelpPage['bigUri']    = AM_Tools::getImageUrl(
                        $oVerticalHelpPage->getResolutionForPreview(),
                        $oVerticalHelpPage->getThumbnailPresetType(),
                        $oVerticalHelpPage->id_issue,
                        $oVerticalHelpPage->type . '.png') . '?' . strtotime($oVerticalHelpPage->updated);
            }

            $oHorizontalHelpPage = AM_Model_Db_Table_Abstract::factory('issue_help_page')
                    ->findOneBy(array('id_issue' => $this->primaryKeyValue, 'type' => AM_Model_Db_IssueHelpPage::TYPE_HORIZONTAL));
            if (!is_null($oHorizontalHelpPage)) {
                $aHorizontalHelpPage['smallUri']  = AM_Tools::getImageUrl(
                        $oHorizontalHelpPage->getThumbnailPresetType(),
                        $oHorizontalHelpPage->getThumbnailPresetType(),
                        $oHorizontalHelpPage->id_issue,
                        $oHorizontalHelpPage->type . '.png') . '?' . strtotime($oHorizontalHelpPage->updated);
                $aHorizontalHelpPage['name']      = $oHorizontalHelpPage->name;
                $aHorizontalHelpPage['nameShort'] = $this->actionController->getHelper('String')->cut($oHorizontalHelpPage->name, 12);
                $aHorizontalHelpPage['bigUri']    = AM_Tools::getImageUrl(
                        $oHorizontalHelpPage->getResolutionForPreview(),
                        $oHorizontalHelpPage->getThumbnailPresetType(),
                        $oHorizontalHelpPage->id_issue,
                        $oHorizontalHelpPage->type . '.png') . '?' . strtotime($oHorizontalHelpPage->updated);
            }
        }

        $aRecord = array(
            'staticPdfMode'      => (isset($sStaticPdfMode) && $sStaticPdfMode) ? $sStaticPdfMode : null,
            'states'             => $aStates,
            'orientations'       => self::$_aValidOrientations,
            'pdf'                => isset($aPdfFiles) ? $aPdfFiles : array(),
            'appId'              => $this->applicationId,
            'last_revision'      => $iLastRevision,
            'simplePdf'          => $aSimplePdf,
            'verticalHelpPage'   => $aVerticalHelpPage,
            'horizontalHelpPage' => $aHorizontalHelpPage
        );

        if (isset($this->view->{$this->getName()})) {
            $aRecord = array_merge($aRecord, $this->view->{$this->getName()});
        }

        $this->view->{$this->getName()} = $aRecord;

        parent::show();
    }

    /**
     * @return boolean
     */
    public function insert()
    {
        $bResult = false;

        $this->databaseControls[] = new Volcano_Component_Control_Database_Static($this->actionController, 'created', new Zend_Db_Expr('NOW()'));

        if (!parent::insert()) {
            return false;
        }

        if ($this->controls['type']->getValue() == AM_Model_Db_Issue::VERTICAL_MODE_ENRICHED) {
            $aUser = $this->actionController->getUser();

            $aBind            = array();
            $aBind['state']   = self::STATE_WORK_IN_PROGRESS;
            $aBind['user']    = $aUser['id'];
            $aBind['created'] = new Zend_Db_Expr('NOW()');
            $aBind['updated'] = new Zend_Db_Expr('NOW()');
            $aBind['issue']   = $this->getPrimaryKeyValue();
            $aBind['title']   = 'First revision';

            if (!$this->db->insert('revision', $aBind)) {
                return false;
            }


            $aBind             = array();
            $aBind['title']    = 'Root page';
            $aBind['template'] = AM_Model_Db_Template::TPL_COVER_PAGE;
            $aBind['revision'] = $this->db->lastInsertId('revision');
            $aBind['user']     = $aUser['id'];
            $aBind['created']  = new Zend_Db_Expr('NOW()');
            $aBind['updated']  = new Zend_Db_Expr('NOW()');

            if (!$this->db->insert('page', $aBind)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    protected function update()
    {
        $bIsPublished = false;

        $oIssue = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $this->primaryKeyValue);
        /* @var $oIssue AM_Model_Db_Issue */
        $sOldState       = $oIssue->state;
        $sOldOrientation = $oIssue->orientation;
        $sOldPdfMode     = $oIssue->static_pdf_mode;
        $sOldType        = $oIssue->type;

        if ($this->controls['state']->getValue() == AM_Model_Db_State::STATE_PUBLISHED) {
            // Check for published revision
            $oQuery = $this->db->select()
                    ->from('revision', 'COUNT(*)')
                    ->where('issue = ?', $this->primaryKeyValue)
                    ->where('state = ?', AM_Model_Db_State::STATE_PUBLISHED);
            $iPubRevisions = $this->db->fetchOne($oQuery);
            if (!$iPubRevisions) {
                $this->errors[] = $this->localizer->translate('Issue has no published revision');

                return false;
            }

            if ($sOldState != AM_Model_Db_State::STATE_PUBLISHED) {
                $bIsPublished = true;

                $this->databaseControls[] = new Volcano_Component_Control_Database_Static(
                            $this->actionController, 'release_date', new Zend_Db_Expr('NOW()'));
            }
        }

        if ($this->controls['orientation']->getValue() != $sOldOrientation) {
            $this->controls['orientation']->setDbValue($sOldOrientation);
        }

        $bResult = parent::update();

        if (!$bResult) {
            return false;
        }

        if ($bIsPublished) {
            $sMessage = $this->actionController->__('New issue is available');

            $oTaskPlanner = new AM_Task_Worker_AppleNotification_Planner();
            $oTaskPlanner->setOptions(array('issue_id' => $this->primaryKeyValue,
                                            'message'  => $sMessage,
                                            'badge'    => 1))
                         ->create();
        }

        if ($this->controls['pdf_type']->getValue() != $sOldPdfMode) {
            $oIssue->static_pdf_mode = $this->controls['pdf_type']->getValue();
            $oIssue->compileHorizontalPdfs();
        }

        if ($this->controls['type']->getValue() != $sOldType) {
            AM_Model_Db_Table_Abstract::factory('issue_simple_pdf')->deleteBy(array('id_issue'=>$this->primaryKeyValue));
        }

        return $bResult;
    }

    protected function _preOperation()
    {
        $sTitle = $this->controls['title']->getValue();
        $this->controls['title']->setValue(AM_Tools::filter_xss($sTitle));

        $sNumber = $this->controls['number']->getValue();
        $this->controls['number']->setValue(AM_Tools::filter_xss($sNumber));

        $sProductId = $this->controls['product_id']->getValue();
        $this->controls['product_id']->setValue(AM_Tools::filter_xss($sProductId));
    }
}

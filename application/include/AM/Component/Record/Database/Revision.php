<?php
/**
 * @file
 * AM_Component_Record_Database_Revision class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Revision record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database_Revision extends AM_Component_Record_Database
{
    /**
     *
     * @param AM_Controller_Action $oActionController
     * @param string $sName
     * @param int $iRevisionId
     * @param int $iIssueId
     * @return void
     */
    public function  __construct(AM_Controller_Action $oActionController, $sName, $iRevisionId, $iIssueId)
    {
        $this->user = $oActionController->getUser();

        $aControls = array();
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'title', 'Title', array(array('require')), 'title');
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'state', 'State', null, 'state');
        $aControls[] = new Volcano_Component_Control($oActionController, 'copy_from', 'Copy from', array(array('integer')));
        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'issue', $iIssueId);
        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'updated', new Zend_Db_Expr('NOW()'));

        if (!$iRevisionId) {
            $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'user', $this->user['id']);
        }

        return parent::__construct($oActionController, $sName, $aControls, $oActionController->oDb, $sName, 'id', $iRevisionId);
    }

    /**
     * @return void
     */
    public function show()
    {
        if (!$this->controls['state']->getValue()) {
            $this->controls['state']->setValue(AM_Model_Db_State::STATE_WORK_IN_PROGRESS);
        }

        $aStates    = array();
        $aRevisions = array(0 => '---');
        if ($this->primaryKeyValue) {
            $oStates = AM_Model_Db_Table_Abstract::factory('state')->fetchAll(null, array('id ASC'));
            foreach ($oStates as $oState) {
                $aStates[$oState->id] = $oState->title;
            }
        } else {
            $aCriteria = ($this->user['is_admin'])? array() : array('user' => $this->user['id']);

            $oRevisions = AM_Model_Db_Table_Abstract::factory('revision')->findAllBy($aCriteria);
            foreach ($oRevisions as $oRevision) {
                $aRevisions[$oRevision->id] = $oRevision->title;
            }
        }

        $aRecord = array(
            'states'              => $aStates,
            'copy_from_revisions' => $aRevisions
        );

        if (isset($this->view->{$this->getName()})) {
            $aRecord = array_merge($aRecord, $this->view->{$this->getName()});
        }

        $this->view->{$this->getName()} = $aRecord;

        parent::show();
    }

    /**
     * @return string
     */
    public function operation()
    {
        $this->_preOperation();

        if ($this->isSubmitted && $this->validate()) {
            $iCopyFromRevisionId = intval($this->controls['copy_from']->getValue());
            $iPrimaryKey         = $this->getPrimaryKeyValue();
            if ($iCopyFromRevisionId && !$iPrimaryKey) {
                return $this->copy();
            } elseif ($iPrimaryKey) {
                return $this->update();
            } else {
                return $this->insert();
            }
        }
        return false;
    }

    /**
     * Copy revision from existing
     *
     * @return boolen
     */
    public function copy()
    {
        $iRevisionFromId = $this->controls['copy_from']->getValue();
        $this->databaseControls[] = new Volcano_Component_Control_Database_Static($this->actionController, 'created', new Zend_Db_Expr('NOW()'));
        $this->controls['state']->setValue(AM_Model_Db_State::STATE_WORK_IN_PROGRESS);
        if (!parent::insert()) {
            return false;
        }

        $oRevisionCurrent = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy('id', $this->getPrimaryKeyValue());
        $oRevisionFrom    = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy('id', $iRevisionFromId);

        if(is_null($oRevisionCurrent) || is_null($oRevisionFrom)) {
            return false;
        }

        try {
            $oRevisionCurrent->copyFromRevision($oRevisionFrom);
        } catch (Exception $oException) {
            return false;
        }
    }

    /**
     * @return boolean
     */
    public function insert()
    {
        $this->databaseControls[] = new Volcano_Component_Control_Database_Static($this->actionController, 'created', new Zend_Db_Expr('NOW()'));
        $this->controls['state']->setValue(AM_Model_Db_State::STATE_WORK_IN_PROGRESS);

        if (!parent::insert()) {
            return false;
        }

        $aBind             = array();
        $aBind['title']    = 'Root page';
        $aBind['template'] = AM_Model_Db_Template::TPL_COVER_PAGE;
        $aBind['revision'] = $this->getPrimaryKeyValue();
        $aBind['user']     = $this->user['id'];
        $aBind['created']  = new Zend_Db_Expr('NOW()');
        $aBind['updated']  = new Zend_Db_Expr('NOW()');

        $this->db->insert('page', $aBind);

        // Update issue
        $this->db->update('issue', array('updated' => new Zend_Db_Expr('NOW()')),
                $this->db->quoteInto('id = ?', $this->controls['issue']->getValue()));

        return true;
    }

    /**
     * @return boolean
     */
    protected function update()
    {
        if ($this->controls['state']->getValue() == AM_Model_Db_State::STATE_PUBLISHED) {
            $aBind = array(
                'state'   => AM_Model_Db_State::STATE_ARCHIVED,
                'updated' => new Zend_Db_Expr('NOW()')
            );
            $aWhere = array(
                $this->db->quoteInto('issue = ?', $this->controls['issue']->getValue()),
                $this->db->quoteInto('state = ?', AM_Model_Db_State::STATE_PUBLISHED)
            );
            $this->db->update('revision', $aBind, $aWhere);
        }

        if (!parent::update()) {
            return false;
        }

        // Update issue
        $this->db->update('issue', array('updated' => new Zend_Db_Expr('NOW()')),
                $this->db->quoteInto('id = ?', $this->controls['issue']->getValue()));

        return true;
    }

    protected function _preOperation()
    {
        $sTitle = $this->controls['title']->getValue();
        $this->controls['title']->setValue(AM_Tools::filter_xss($sTitle));
    }
}

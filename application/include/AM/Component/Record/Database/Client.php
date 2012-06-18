<?php
/**
 * @file
 * AM_Component_Record_Database_Client class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Client record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database_Client extends AM_Component_Record_Database
{
    /**
     * @param AM_Controller_Action $oActionController
     * @param string $sName
     * @param int $iId
     * @return viod
     */
    public function  __construct(AM_Controller_Action $oActionController, $sName, $iId)
    {
        $aControls   = array();
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'title', 'Name', array(array('require')), 'title');

        return parent::__construct($oActionController, $sName, $aControls, $oActionController->oDb, 'client', 'id', $iId);
    }

    /**
     * @return boolean
     */
    public function  validate()
    {
        if (!parent::validate()) {
            return false;
        }

        if (!$this->primaryKeyValue) {
            $oQuery = $this->db->select()->from('client', 'id')
                              ->where('title = ?', $this->controls['title']->getValue())
                              ->where('deleted != ?','yes');

            if ($this->db->fetchOne($oQuery)) {
                $this->errors[] = $this->actionController->localizer->translate('Client with such name already exists');

                return false;
            }
        }

        return true;
    }

    protected function _preOperation()
    {
        $sTitle = $this->controls['title']->getValue();
        $this->controls['title']->setValue(AM_Tools::filter_xss($sTitle));
    }
}
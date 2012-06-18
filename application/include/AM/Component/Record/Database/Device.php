<?php
/**
 * @file
 * AM_Component_Record_Database_Device class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Device record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database_Device extends AM_Component_Record_Database
{
    public function  __construct(AM_Controller_Action $oActionController, $sName, $iId)
    {
        $aControls   = array();
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'identifer', 'UDID', array(array('require')));
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'user', 'User');
        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'created', new Zend_Db_Expr('NOW()'));

        return parent::__construct($oActionController, $sName, $aControls, $oActionController->oDb, 'device', 'id', $iId);
    }

    public function show() {
        $oQuery = $this->db->select()->from('user', null)
                ->joinLeft('client', 'client.id = user.client', null)
                ->where('user.deleted = "no"')
                ->order(array('user.first_name', 'user.last_name'))
                ->columns(array(
                    'id'   => 'user.id',
                    'name' => 'CONCAT(user.first_name, " ", user.last_name, IF(client.title IS NOT NULL, CONCAT(" (", client.title, ")"), ""), IF(user.is_admin, " - admin", ""))'
                ));

        $aUsers = array('' => $this->actionController->__('Select user'))
                + $this->db->fetchPairs($oQuery);

        $aRecord = array(
            'users' => $aUsers
        );

        if (isset($this->view->{$this->getName()})) {
            $aRecord = array_merge($aRecord, $this->view->{$this->getName()});
        }

        $this->view->{$this->getName()} = $aRecord;

        parent::show();
    }

}

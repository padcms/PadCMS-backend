<?php
/**
 * @file
 * AM_Component_Record_Database_Device class definition.
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
 * Device record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database_Device extends AM_Component_Record_Database
{
    public function  __construct(AM_Controller_Action $oActionController, $sName, $iId)
    {
        $aControls   = array();
        $aControls[] = new Volcano_Component_Control_Database($oActionController, 'identifer', 'UDID', array(array('require'), array('maxlen', 60)));
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

    protected function _preOperation()
    {
        $sIdentifer = $this->controls['identifer']->getValue();
        $this->controls['identifer']->setValue(AM_Tools::filter_xss($sIdentifer));
    }
}

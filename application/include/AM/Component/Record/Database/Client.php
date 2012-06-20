<?php
/**
 * @file
 * AM_Component_Record_Database_Client class definition.
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
<?php
/**
 * @file
 * AM_Component_List_Devices class definition.
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
 * Devices list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_Devices extends AM_Component_Grid
{
    /** @var AM_Component_Filter */
    protected $_oFilterComponent; /**< @type AM_Component_Filter */

    /**
     * @param AM_Controller_Action $oActionController
     * @param string $sName
     * @param AM_Component_Filter $oFilterComponent
     */
    public function __construct(AM_Controller_Action $oActionController, $sName, AM_Component_Filter $oFilterComponent)
    {
        $this->_oFilterComponent = $oFilterComponent;

        $oQuery = $oActionController->oDb->select()
                ->from('device', null)
                ->joinLeft('user', 'user.id = device.user AND user.deleted = "no"', null)
                ->joinLeft('client', 'client.id = user.client AND user.id IS NOT NULL AND client.deleted = "no"', null)
                ->where('device.deleted = "no"')
                ->columns(array(
                    'id' => 'device.id',
                    'identifer' => 'device.identifer',
                    'created' => 'device.created',
                    'user' => 'CONCAT(user.first_name, " ", user.last_name, IF(client.title IS NOT NULL, CONCAT(" (", client.title, ")"), ""), IF(user.is_admin, " - admin", ""))'
                ));

        parent::__construct($oActionController, $sName, $oActionController->oDb, $oQuery, 'created DESC',
                array(
                    'identifer' => 'identifer',
                    'created' => 'created',
                    'user' => 'user'
                ),
                10,
                'subselect');
    }

    /**
     * Component post initialization
     */
    protected function postInitialize() {
        parent::postInitialize();
        if ($sValue = $this->_oFilterComponent->getControl('identifer')->getValue()) {
            $this->selectSQL->where('device.identifer LIKE CONCAT("%", ?, "%")', $sValue);
        }
        if ($sValue = $this->_oFilterComponent->getControl('linked')->getValue()) {
            $this->selectSQL->where('device.user IS NOT NULL');
        }
    }
}
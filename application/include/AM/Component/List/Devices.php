<?php
/**
 * @file
 * AM_Component_List_Devices class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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
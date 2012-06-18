<?php
/**
 * @file
 * AM_Component_List_Settings class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Setings list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_Settings extends AM_Component_Grid {

    /**
     * @param AM_Controller_Action $oActionController
     */
    public function __construct(AM_Controller_Action $oActionController)
    {
        $oQuery = $oActionController->oDb->select()->from('settings');
        parent::__construct($oActionController,
                'grid',
                $oActionController->oDb,
                $oQuery,
                'name',
                null,
                4,
                'subselect');
    }
}
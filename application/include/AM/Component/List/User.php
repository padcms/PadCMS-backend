<?php
/**
 * @file
 * AM_Component_List_User class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Users list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_User extends AM_Component_Grid
{
    /**
     * @param AM_Controller_Action $oActionController
     * @param int $iClientId
     */
    public function __construct(AM_Controller_Action $oActionController, $iClientId)
    {
        $oQuery = $oActionController->oDb->select()
                ->from('user')
                ->where('deleted = ?', 'no');

        if (!is_null($iClientId)) {
            $oQuery->where('client = ?', $iClientId);
        }

        parent::__construct($oActionController, 'grid', $oActionController->oDb, $oQuery, 'user.last_name, user.first_name', null, 4, 'subselect');
    }
}
<?php
/**
 * @file
 * AM_Component_List_Client class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Clients list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_Client extends AM_Component_Grid {

    public function __construct(AM_Controller_Action $oActionController)
    {
        $oQuery = $oActionController->oDb->select()
            ->from('client')

            ->joinLeft('user', 'user.client = client.id AND user.deleted = "no"',
                  array('user_count' => new Zend_Db_Expr('COUNT(DISTINCT(user.id))')))

            ->joinLeft('application', 'application.client = client.id AND application.deleted = "no"',
                  array('application_count' => new Zend_Db_Expr('COUNT(DISTINCT(application.id))')))

            ->where('client.deleted = ?', 'no')

            ->group(array('client.id'));

        parent::__construct($oActionController, 'grid', $oActionController->oDb, $oQuery, 'client.title', array(), 4, 'subselect');
    }
}
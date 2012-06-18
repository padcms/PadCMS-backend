<?php
/**
 * @file
 * AM_Component_List_Issue class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Issues list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_Issue extends AM_Component_Grid
{
    /**
     * @param AM_Controller_Action $oActionController
     * @param int $iApplicationId
     */
    public function __construct(AM_Controller_Action $oActionController, $iApplicationId)
    {
        $iApplicationId = intval($iApplicationId);
        $aUser          = $oActionController->getUser();
        $sUserRole      = $aUser['role'];

        $oQuery = $oActionController->oDb->select()
                ->from('issue', null)

                ->join('application', 'application.id = issue.application', null)
                ->join('state', 'issue.state = state.id', null)
                ->join('user', 'user.id = issue.user', null)

                ->joinLeft('revision', 'revision.issue = issue.id', null)
                ->joinLeft(array('revision1' => 'revision'), 'revision1.issue = issue.id '
                        . $oActionController->oDb->quoteInto('AND revision1.state = ?', AM_Model_Db_State::STATE_PUBLISHED), null)

                ->where('application.id = ?', $iApplicationId)

                ->where('application.deleted = ?', 'no')
                ->where('issue.deleted = ?', 'no')

                ->where('user.deleted = ?', 'no')

                ->group(array('issue.id'))

                ->columns(array(
                        'id'                => 'issue.id',
                        'title'             => 'issue.title',
                        'number'            => 'issue.number',
                        'client'            => 'application.client',
                        'state'             => 'state.title',
                        'last_revision'     => 'MAX(revision.id)',
                        'published_revision'=> 'revision1.id',
                        'created'           => 'DATE_FORMAT(issue.created, "%e/%c/%Y")',
                        'updated_date'      => 'DATE_FORMAT(issue.updated, "%e/%c/%Y")',
                        'updated_time'      => 'DATE_FORMAT(issue.updated, "%Hh%i")',
                        'release_date'      => 'DATE_FORMAT(issue.release_date, "%e/%c/%Y at %Hh%i")',
                        'release_date_ts'   => 'UNIX_TIMESTAMP(issue.release_date)',
                        'creator_full_name' => 'CONCAT(user.first_name, " ", user.last_name)',
                        'creator_uid'       => 'user.id',
                        'creator_role'      => 'IF(user.is_admin, "admin", "user")',
                        'application_id'    => 'application.id'));

                 if ("admin" != $sUserRole) {
                    $oQuery->where('user.client = application.client');
                 }

        parent::__construct($oActionController, 'grid', $oActionController->oDb,
                $oQuery, 'issue.updated DESC', null, 4, 'subselect');
    }
}
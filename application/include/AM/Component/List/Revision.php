<?php
/**
 * @file
 * AM_Component_List_Revision class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Revisions list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_Revision extends AM_Component_Grid {

    /**
     * @param AM_Controller_Action $oActionController
     * @param int $iIssueId
     */
    public function __construct(AM_Controller_Action $oActionController, $iIssueId)
    {
        $oQuery = $oActionController->oDb->select()
                ->from('revision', null)

                ->join('issue', 'issue.id = revision.issue', null)
                ->join('state', 'state.id = revision.state', null)
                ->join('user', 'user.id = revision.user', null)

                ->columns(array(
                    'id'                => 'revision.id',
                    'title'             => 'revision.title',
                    'state'             => 'state.title',
                    'issue'             => 'issue.id',
                    'created'           => 'DATE_FORMAT(revision.created, "%e/%c/%Y %Hh%i")',
                    'updated'           => 'DATE_FORMAT(revision.updated, "%e/%c/%Y %Hh%i")',
                    'creator_full_name' => 'CONCAT(user.first_name, " ", user.last_name)',
                    'creator_uid'       => 'user.id',
                    'creator_role'      => 'IF(user.is_admin, "admin", "user")',
                    'revision_id'       => 'revision.id'
                ))

                ->where('revision.deleted = ?', 'no')
                ->where('issue.deleted = ?', 'no')
                ->where('user.deleted = ?', 'no')

                ->where('issue.id = ?', $iIssueId);

        parent::__construct($oActionController, 'grid', $oActionController->oDb,
                $oQuery, 'revision.updated DESC', null, 4, 'subselect');
    }
}
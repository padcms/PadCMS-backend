<?php
/**
 * @file
 * AM_Component_List_Revision class definition.
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
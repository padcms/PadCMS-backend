<?php
/**
 * @file
 * AM_Component_List_Application class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Applications list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_Application extends AM_Component_Grid
{
    public function __construct(AM_Controller_Action $oActionController, $iClientId)
    {
        $iClientId = intval($iClientId);

        $oQuery = $oActionController->oDb->select()
                ->from('application')

                ->joinLeft('issue', 'issue.application = application.id AND issue.deleted = "no"', null)

                ->joinLeft(array('issue1' => 'issue'),
                        'application.id = issue1.application AND issue1.deleted = "no" '
                            . $oActionController->oDb->quoteInto('AND issue1.state = ?', AM_Model_Db_State::STATE_PUBLISHED), null)

                ->joinLeft('revision',
                        'revision.issue = issue1.id AND revision.deleted = "no" '
                            . $oActionController->oDb->quoteInto('AND revision.state = ?', AM_Model_Db_State::STATE_PUBLISHED), null)

                ->where('application.deleted = ?', 'no')
                ->where('application.client = ?', $iClientId)

                ->group('application.id')

                ->columns(array(
                    'published_revision' => 'revision.id',
                    'issue_count' => new Zend_Db_Expr('COUNT(DISTINCT(issue.id))')
                ));

        parent::__construct($oActionController, 'grid', $oActionController->oDb, $oQuery, 'application.title', array(), 4, 'subselect');
    }
}
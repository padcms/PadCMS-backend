<?php
/**
 * @file
 * AM_Component_List_Issue class definition.
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
 * Issues list component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_List_Subscription extends AM_Component_Grid
{
    /**
     * @param AM_Controller_Action $oActionController
     * @param int $iApplicationId
     */
    public function __construct(AM_Controller_Action $oActionController, $iApplicationId)
    {
        $iApplicationId = intval($iApplicationId);

        $oQuery = $oActionController->oDb->select()
                ->from('subscription', null)

                ->join('application', 'application.id = subscription.application', null)

                ->where('application.id = ?', $iApplicationId)

                ->where('application.deleted = ?', 'no')

                ->columns(array(
                        'id'             => 'subscription.id',
                        'itunes_id'      => 'subscription.itunes_id',
                        'google_id'      => 'subscription.google_id',
                        'button_title'   => 'subscription.button_title',
                        'application_id' => 'application.id'));

        parent::__construct($oActionController, 'grid', $oActionController->oDb,
                $oQuery, 'subscription.id', null, null, 'subselect');
    }
}

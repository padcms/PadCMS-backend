<?php
/**
 * @file
 * AM_Component_Record_Database_Issue_Abstract class definition.
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
 * Issue record component
 * @ingroup AM_Component
 */
abstract class AM_Component_Record_Database_Issue_Abstract extends AM_Component_Record_Database
{

    /**
     * @param string $sType Application type
     *
     * @return string|null
     */
    public static function getClassByApplicationType($sType) {
        switch ($sType) {
            case AM_Model_Db_ApplicationType::TYPE_GENERIC:
                return 'AM_Component_Record_Database_Issue_Generic';
            case AM_Model_Db_ApplicationType::TYPE_RUE98WE:
                return 'AM_Component_Record_Database_Issue_Rue89we';
        }
        return null;
    }

    public function  __construct(AM_Controller_Action $oActionController, $sName, $iIssueId, $iApplicationId)
    {
        $this->applicationId = $iApplicationId;

        $aControls   = array();

        $aUser = $oActionController->getUser();

        if (!$iIssueId) {
            $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'user', $aUser['id']);
        }

        return parent::__construct($oActionController,
                $sName, $aControls, $oActionController->oDb, 'issue', 'id', $iIssueId);
    }
}

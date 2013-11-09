<?php
/**
 * @file
 * AM_Component_Record_Database_Issue_Generic class definition.
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
class AM_Component_Record_Database_Subscription extends AM_Component_Record_Database
{
    public function  __construct(AM_Controller_Action $oActionController, $sName, $iSubscriptionId, $iApplicationId)
    {
        $aControls   = array();

        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'application', $iApplicationId);

        $aControls[] = new Volcano_Component_Control_Database($oActionController,
            'itunes_id',
            'Itunes id');

        $aControls[] = new Volcano_Component_Control_Database($oActionController,
            'google_id',
            'Google id');

        $aControls[] = new Volcano_Component_Control_Database($oActionController,
            'button_title',
            'Button title', array(array('require')));

        $aControls[] = new Volcano_Component_Control_Database_Static($oActionController, 'updated', new Zend_Db_Expr('NOW()'));

        return parent::__construct($oActionController,
            $sName, $aControls, $oActionController->oDb, 'subscription', 'id', $iSubscriptionId);
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        if (!parent::validate()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function operation()
    {
        return parent::operation();
    }

    /**
     * @return void
     */
    public function show()
    {
        parent::show();
    }

    /**
     * @return boolean
     */
    public function insert()
    {
        $this->databaseControls[] = new Volcano_Component_Control_Database_Static($this->actionController, 'created', new Zend_Db_Expr('NOW()'));

        return parent::insert();
    }

    /**
     * @return boolean
     */
    protected function update()
    {
        return parent::update();
    }

}

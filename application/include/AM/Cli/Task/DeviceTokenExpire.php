<?php
/**
 * @file
 * AM_Cli_Task_DeviceTokenClear class definition.
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
 * The Device Token Clear
 * @ingroup AM_Cli
 */
class AM_Cli_Task_DeviceTokenExpire extends AM_Cli_Task_Abstract
{
    protected function _configure()
    {}

    public function execute()
    {
        $rowCount = AM_Model_Db_Table_Abstract::factory('device_token')
            ->getAdapter()->update('device_token',
                array('device_token.expired' => new Zend_Db_Expr('NOW()')),
                array(
                    'device_token.updated < ?' => new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'),
                    'device_token.expired ?' => new Zend_Db_Expr('IS NULL'),
                ));
        $this->getLogger()->debug(sprintf('%d tokens expired.', $rowCount));
    }
}

<?php
/**
 * @file
 * AM_Test_PHPUnit_Database_Constraint_TableIsEqual class definition.
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
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Test
 */
class AM_Test_PHPUnit_Database_Constraint_TableIsEqual extends PHPUnit_Extensions_Database_Constraint_TableIsEqual
{
    /**
     * Determines whether or not the given table matches the table used to
     * create this constraint.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $oDataSet
     * @return bool
     */
    public function evaluate($oDataSet)
    {
        if ($oDataSet instanceof PHPUnit_Extensions_Database_DataSet_ITable) {
            try {
                $this->value->assertEquals($oDataSet);
                return TRUE;
            } catch (Exception $e) {
                $this->failure_reason = $e->getMessage();
                $this->fail($oDataSet, '');
                return FALSE;
            }
        } else {
            throw new InvalidArgumentException("PHPUnit_Extensions_Database_DataSet_ITable expected");
        }
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf('%s Reason: %s', PHPUnit_Util_Type::export($this->value), $this->failure_reason);
    }
}
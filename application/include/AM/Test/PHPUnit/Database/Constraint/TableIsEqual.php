<?php
/**
 * @file
 * AM_Test_PHPUnit_Database_Constraint_TableIsEqual class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
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
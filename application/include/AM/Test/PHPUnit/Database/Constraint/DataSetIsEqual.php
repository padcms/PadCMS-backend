<?php
/**
 * @file
 * AM_Test_PHPUnit_Database_Constraint_DataSetIsEqual class definition.
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
class AM_Test_PHPUnit_Database_Constraint_DataSetIsEqual extends PHPUnit_Extensions_Database_Constraint_DataSetIsEqual
{
    /**
     * Determines whether or not the given dataset matches the dataset used to
     * create this constraint.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $oDataSet
     * @return bool
     */
    public function evaluate($oDataSet)
    {
        if ($oDataSet instanceof PHPUnit_Extensions_Database_DataSet_IDataSet) {
            try {
                $this->value->assertEquals($oDataSet);
                return TRUE;
            } catch (Exception $oException) {
                $this->failure_reason = $oException->getMessage();
                $this->fail($oDataSet, '');
            }
        } else {
            throw new InvalidArgumentException("PHPUnit_Extensions_Database_DataSet_IDataSet expected");
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
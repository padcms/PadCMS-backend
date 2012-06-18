<?php

class PurchaseIsExpiredTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'PurchaseIsExpiredTest.yml';
    }

    public function testShouldReturnTrueWhenCheckExpiredPayment()
    {
        //GIVEN
        $oPurchase               = AM_Model_Db_Table_Abstract::factory('purchase')->findOneBy(array('id' => 1));

        $oDate = new Zend_Date();
        $oDate->setTimestamp(time() - 3600000);

        $oPurchase->expires_date = $oDate->toString('Y-m-d H:i:s');
        $oPurchase->save();

        //WHEN
        $bResult = $oPurchase->isExpired();

        //THEN
        $this->assertTrue($bResult);
    }

    public function testShouldReturnTrueWhenCheckNotExpiredPayment()
    {
        //GIVEN
        $oPurchase               = AM_Model_Db_Table_Abstract::factory('purchase')->findOneBy(array('id' => 1));

        $oDate = new Zend_Date();
        $oDate->setTimestamp(time() + 3600000);

        $oPurchase->expires_date = $oDate->toString('Y-m-d H:i:s');
        $oPurchase->save();

        //WHEN
        $bResult = $oPurchase->isExpired();

        //THEN
        $this->assertFalse($bResult);
    }
}
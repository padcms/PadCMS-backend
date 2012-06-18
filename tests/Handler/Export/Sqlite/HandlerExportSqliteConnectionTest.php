<?php

class HandlerExportSqliteConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testShouldEstablishConnection()
    {
        //GIVEN
        $handler = new AM_Handler_Export_Sqlite();

        //WHEN
        $connection = $handler->getAdapter();

        //THEN
        $this->assertTrue($connection instanceof Zend_Db_Adapter_Pdo_Sqlite);
    }
}
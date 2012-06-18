<?php

class TermGetTocAsTreeTest extends AM_Test_PHPUnit_DatabaseTestCase
{
    protected function _getDataSetYmlFile()
    {
        return dirname(__FILE__)
                . DIRECTORY_SEPARATOR . '_fixtures'
                . DIRECTORY_SEPARATOR . 'TermGetTocAsTreeTest.yml';
    }

    public function testShouldGetTocAsTree()
    {
        //GIVEN
        $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy(array('id' => 1));

        $aExpectedResult = array(
            array(
                    'parent_term' => null,
                    'attr'        => array('id' => 1),
                    'data'        => '1',
                    'children'    => array(
                        array(
                            'parent_term' => 1,
                            'attr'        => array('id' => 3),
                            'data'        => '1_1',
                            'children'    => array(
                                array(
                                    'parent_term' => 3,
                                    'attr'        => array('id' => 7),
                                    'data'        => '1_1_1',
                                    'children'    => array()
                                )
                            )
                        ),
                        array(
                            'parent_term' => 1,
                            'attr'        => array('id' => 4),
                            'data'        => '1_2',
                            'children'    => array()
                        )
                    )
            ),
            array(
                    'parent_term' => null,
                    'attr'        => array('id' => 2),
                    'data'        => '2',
                    'children'    => array(
                        array(
                            'parent_term' => 2,
                            'attr'        => array('id' => 5),
                            'data'        => '2_1',
                            'children'    => array()
                        ),
                        array(
                            'parent_term' => 2,
                            'attr'        => array('id' => 6),
                            'data'        => '2_2',
                            'children'    => array()
                        )
                    )
            )
        );

        //WHEN
        $aResult = AM_Model_Db_Table_Abstract::factory('term')->getTocAsTree($oRevision);

        //THEN
        $this->assertEquals($aExpectedResult, $aResult);
    }
}

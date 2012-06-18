<?php

class PageNodeTest extends PHPUnit_Framework_TestCase
{
    public function testShouldAddChild()
    {
        //GIVEN
        $root  = new AM_Model_Db_Page(array("data" => array("id" => 1)));
        $child = new AM_Model_Db_Page(array("data" => array("id" => 2)));

        //WHEN
        $root->addChild($child);

        $expectedChilds = array($child);

        //THEN
        $this->assertEquals($expectedChilds, $root->getChilds(), "Root node has wrong childs");
    }

    public function testShouldThrowExceptionWhenSetWrongLinkType()
    {
        //GIVEN
        $root  = new AM_Model_Db_Page(array("data" => array("id" => 1)));

        //WHEN
        try {
            $root->setLinkType("WRONG TYPE");
        //GIVEN
        } catch (AM_Exception $e) {
            $this->assertEquals('Wrong link type given "WRONG TYPE"', $e->getMessage());
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testShouldWalkOnTreeUsingIterator()
    {
        //GIVEN
        $root  = new AM_Model_Db_Page(array("data" => array("id" => 1)));
        $child = new AM_Model_Db_Page(array("data" => array("id" => 2)));

        $root->addChild($child);

        $expected_nodes = array( "1" => $root, "2" => $child);

        $nodes = array();

        //WHEN
        foreach ($root as $key => $node) {
            $nodes[$key] = $node;
        }

        //THEN
        $this->assertEquals($expected_nodes, $nodes, "Wrong nodes given while walk on tree");

    }

}

<?php
/**
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

<?php
/**
 * @file
 * AM_Model_Db_Base_NestedSet class definition.
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
 * The base model for nested sets
 * @ingroup AM_Model
 */
class AM_Model_Db_Base_NestedSet extends AM_Model_Db_Abstract implements AM_Tree_Node_Interface, IteratorAggregate
{
    /** @var array */
    protected $_aNodes    = array(); /**< @type array */
    /** @var AM_Model_Db_Base_NestedSet */
    protected $_oParent   = null; /**< @type AM_Model_Db_Base_NestedSet */

    /**
     * @see AM_Tree_NodeInterface::addChild()
     * @param AM_Model_Db_Base_NestedSet $oNode
     * @return AM_Model_Db_Base_NestedSet
     */
    public final function addChild(AM_Tree_Node_Interface $oNode)
    {
        $this->_aNodes[] = $oNode;

        $oNode->setParent($this);

        return $this;
    }

    /**
     * @see AM_Tree_NodeInterface::getChilds()
     * @return array
     */
    public final function getChilds()
    {
        return $this->_aNodes;
    }

    /**
     * @see AM_Tree_NodeInterface::getIterator()
     * @return Iterator
     */
    public function getIterator()
    {
        return new AM_Tree_Iterator_Down($this);
    }

    /**
     * @see AM_Tree_NodeInterface::getParent()
     * @return AM_Model_Db_Base_NestedSet
     */
    public function getParent()
    {
        return $this->_oParent;
    }

    /**
     * @see AM_Tree_NodeInterface::setParent()
     * @return AM_Model_Db_Base_NestedSet
     */
    public final function setParent(AM_Tree_Node_Interface $oNode)
    {
        $this->_oParent = $oNode;

        return $this;
    }
}

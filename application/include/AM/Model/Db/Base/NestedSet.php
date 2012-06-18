<?php
/**
 * @file
 * AM_Model_Db_Base_NestedSet class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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

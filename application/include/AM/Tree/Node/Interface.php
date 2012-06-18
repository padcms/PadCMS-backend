<?php
/**
 * @file
 * AM_Tree_Node_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Tree
 */

/**
 * Simple tree node
 * @ingroup AM_Tree
 */
interface AM_Tree_Node_Interface
{
    /**
     * Set parrent node
     * @param Zend_Db_Table_Row_Abstract $oNode
     */
    public function setParent(AM_Tree_Node_Interface $oNode);

    /**
     * Get node parent
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getParent();

    /**
     * Add child
     * @param Zend_Db_Table_Row_Abstract $oNode
     */
    public function addChild(AM_Tree_Node_Interface $oNode);

    /**
     * Get node childs
     * @return array
     */
    public function getChilds();

    /**
     * @return Iterator
     */
    public function getIterator();
}

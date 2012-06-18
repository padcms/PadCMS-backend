<?php
/**
 * @file
 * AM_Tree_NodeInterface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Tree down iterator
 * @ingroup AM_Tree
 */
class AM_Tree_Iterator_Down implements Iterator
{
    /** @var array */
    protected $_aStack = array(); /**< @type array */

    /** @var AM_Tree_Node_Interface **/
    protected $_oRoot = null; /**< @type AM_Tree_NodeInterface */

    /** @var AM_Tree_Node_Interface **/
    protected $_oCurrent = null; /**< @type AM_Tree_NodeInterface */

    /**
     * @param AM_Tree_Node_Interface $oRootNode
     */
    public function __construct(AM_Tree_Node_Interface $oRootNode)
    {
        $this->_oRoot = $oRootNode;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return AM_Tree_Node_Interface
     */
    public function current()
    {
        return $this->_oCurrent;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->_oCurrent->id;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $aNodes = $this->_oCurrent->getChilds();
        foreach ($aNodes as $oNode) {
            array_push($this->_aStack, $oNode);
        }

        $this->_oCurrent = array_pop($this->_aStack);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->_oCurrent = $this->_oRoot;
        array_push($this->_aStack, $this->_oRoot);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return !empty($this->_aStack);
    }
}

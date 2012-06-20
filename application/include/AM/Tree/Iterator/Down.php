<?php
/**
 * @file
 * AM_Tree_NodeInterface class definition.
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

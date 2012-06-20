<?php
/**
 * @file
 * AM_Component_Grid class definition.
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
 * Grid component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Grid extends Volcano_Component_Grid
{
    /** @var string */
    protected $_sPageSizeURI = null; /**< @type string */

    /**
     * @param AM_Controller_Action $oActionController
     * @param string $sName
     * @param Zend_Db_Adapter_Abstract $oDbAdapter
     * @param Zend_Db_Select $oSelectSQL
     * @param string $sDefaultSortOrder
     * @param array $aSortOrders
     * @param mixed $mPageSize intefer | array
     * @param string $sCountSQLType
     */
    public function __construct(AM_Controller_Action $oActionController, $sName, Zend_Db_Adapter_Abstract $oDbAdapter, $oSelectSQL, $sDefaultSortOrder = null, $aSortOrders = null, $mPageSize = null, $sCountSQLType = null)
    {
        $mPageSize = $oActionController->getRequest()->getParam($sName.'PageSize', $mPageSize);
        parent::__construct($oActionController, $sName, $oDbAdapter, $oSelectSQL, $sDefaultSortOrder, $aSortOrders, $mPageSize, $sCountSQLType);
    }

    /**
     * @return string
     */
    public function getPagerURI()
    {
        return $this->view->{$this->name}['pagerURI'];
    }

    /**
     * @string type
     */
    public function getPageSizeURI()
    {
        return $this->_sPageSizeURI;
    }

    /**
     * @return int
     */
    public function getTotalRows()
    {
        return $this->view->{$this->name}['totalRows'];
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->view->{$this->name}['totalPages'];
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param array $aRow
     */
    public function rowRender(&$aRow)
    {
    }

    /**
     * @param array $aRows
     */
    public function rowsRender(&$aRows)
    {
    }

    public function show()
    {
        parent::show();

        $aView = $this->view->{$this->getName()};
        //set URI for subcomponent pager
        $aParams               = $this->request->getParams();
        $aParams['controller'] = $this->request->getControllerName();
        $aParams['action']     = $this->request->getActionName();
        if (array_key_exists($this->getName() . 'PageSize', $aParams)) {
            unset($aParams[$this->getName() . 'PageSize']);
        }
        $this->_sPageSizeURI  = $this->actionController->getHelper('Url')->url($aParams, null, true) . '/';
        $aView['pageSizeURI'] = $this->_sPageSizeURI;

        foreach ($aView["rows"] as &$row) {
            $this->rowRender($row);
        }

        $this->rowsRender($aView["rows"]);

        $this->view->{$this->getName()} = $aView;
    }
}
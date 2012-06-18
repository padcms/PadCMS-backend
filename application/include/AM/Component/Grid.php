<?php
/**
 * @file
 * AM_Component_Grid class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
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
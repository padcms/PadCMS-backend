<?php

/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component
 * @subpackage Grid
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */
include_once 'Volcano/Component.php';
include_once 'Volcano/Component/Interface/Database.php';

/**
 * Grid component
 *
 * @todo using standard zend methods for link constructions
 *
 * @category Volcano
 * @package Volcano_Component
 * @subpackage Grid
 */
class Volcano_Component_Grid extends Volcano_Component implements Volcano_Component_Interface_Database {

    /**
     * Database link
     * @var Zend_Db_Adapter_Pdo_Abstract
     */
    protected $db;
    /**
     * Allowed orders
     * @var array
     */
    protected $sortOrders = array();
    /**
     * Name of component
     * @var integer
     */
    protected $name;
    /**
     * Default order
     * @var string
     */
    protected $defaultSortOrder;
    /**
     * Current sort order
     * @var string
     */
    protected $sortOrder;
    /**
     * Sort direction
     */
    protected $sortDirection = "ASC";
    /**
     * SQL command to fetch data
     * @var string;
     */
    protected $selectSQL;
    /**
     * Page size
     * @param integer
     */
    protected $pageSize = 25;
    /**
     * Is page size can be changed from request
     *
     * @var bool
     */
    protected $allowChangePageSize = false;
    /**
     * Maximum page size allowed from request
     *
     * @var int
     */
    protected $maxPageSize = null;
    /**
     * Minimal page size allowed from request
     *
     * @var int
     */
    protected $minPageSize = 1;
    /**
     * Current page
     * @param integer
     */
    protected $page = 1;
    /**
     * Type of count sql. Leave empty for autoselecting
     * allowed values: subselect, simple, "null"
     *
     * @var string
     */
    protected $countSQLType = null;

    /**
     * Constructor
     *
     * @param AM_Controller_Action $actionController Controller
     * @param string $name Component name
     * @param Zend_Db_Adapter_Abstract Database Link
     * @param string $selectSQL SQL for selecting data
     * @param string $defaultSortOrder Default order
     * @param array $sortOrders Allowed orders
     * @param int/array $pageSize Page size or array($defaultPageSize, [$maxPageSize[, $minPageSize]]). In latest case pageSize can be setted from url
     * @param null/string $countSQLType How to coalculate count of rows. Leave empty for autodeterminating. Allowed values: SQL_CALC_FOUND_ROWS, simple, subselect
     */
    public function __construct(AM_Controller_Action $actionController, $name, Zend_Db_Adapter_Abstract $db = null, $selectSQL, $defaultSortOrder = null, $sortOrders = null, $pageSize = null, $countSQLType = null) {
        $this->db = $db;
        $this->selectSQL = $selectSQL;
        if ($defaultSortOrder) {
            $this->defaultSortOrder = $defaultSortOrder;
        }
        if ($sortOrders) {
            $this->sortOrders = $sortOrders;
        }
        if ($pageSize) {
            if (is_array($pageSize)) {
                $this->pageSize = $pageSize[0];
                $this->allowChangePageSize = true;
                if (count($pageSize) > 1) {
                    $this->maxPageSize = $pageSize[1];
                }
                if (count($pageSize) > 2) {
                    $this->minPageSize = $pageSize[2];
                }
            } else {
                $this->pageSize = $pageSize;
            }
        }
        if ($countSQLType) {
            $this->countSQLType = $countSQLType;
        }
        parent::__construct($actionController, $name);
    }

    /**
     * Parse url parameters for finding correct page, sorting order and sorting directory
     *
     * @param string $prefix Variables prefix
     * @param string
     */
    private function parseURLParameters() {
        $this->page = intval($this->getParam("Page", 1));
        if ($this->page <= 0) {
            $this->page = 1;
        }
        if ($this->allowChangePageSize) {
            $proposedPageSize = $this->getParam("PageSize");
            if ($proposedPageSize) {
                $proposedPageSize = (int) $proposedPageSize;
                if ($proposedPageSize <= $this->maxPageSize && $proposedPageSize >= $this->minPageSize) {
                    $this->pageSize = $proposedPageSize;
                }
            }
        }

        $sortOrder = $this->getParam("Order");
        if (isset($this->sortOrders[$sortOrder])) {
            $this->sortDirection = $this->getParam("Dir", "ASC") == "ASC" ? "ASC" : " DESC";
            $this->sortOrder = $sortOrder;
        } else {
            $this->sortOrder = $this->defaultSortOrder;
        }
    }

    public function show() {

        $vars = array("name" => $this->name);

        //get data from DB
        $this->parseURLParameters();

        $totalPages = $totalRows = 0;
        $pagingEnabled = ($this->pageSize > 0);
        $countSQLInMainSelect = false;

        if ($pagingEnabled) {
            //use direct count if there's no distinct inside of SELECT statement
            //that's 10 times faster than count loaded results
            $countSQLType = $this->countSQLType;
            if (!$countSQLType) {
                if (in_array(get_class($this->db), array('Zend_Db_Adapter_Mysqli', 'Zend_Db_Adapter_Pdo_Mysql'))) {
                    $countSQLType = "SQL_CALC_FOUND_ROWS";
                } elseif (preg_match("/\\s((distinct)|(group by))\\s/i", $this->selectSQL)) {
                    $countSQLType = "subselect";
                } else {
                    $countSQLType = "simple";
                }
            }
            switch ($countSQLType) {
                case 'SQL_CALC_FOUND_ROWS':
                    $countSQL = preg_replace("/(^\\s*)SELECT/smi", '$1SELECT SQL_CALC_FOUND_ROWS ', $this->selectSQL);
                    $countSQLInMainSelect = true;
                    break;
                case 'simple':
                    //SELECT.+\\sFROM is greedy to replace cases with multi selects
                    $countSQL = "SELECT COUNT(*) FROM " . preg_replace("/^\\s*SELECT.+\\sFROM\\s/smi", "", $this->selectSQL);
                    break;
                case 'subselect':
                default:
                    $countSQL = "select count(*) from (" . $this->selectSQL . ") a";
            }
            if (!$countSQLInMainSelect) {
                $totalRows = $this->db->fetchOne($countSQL);
                $totalPages = ceil($totalRows / $this->pageSize);
            } else {
                $totalRows = $this->page * $this->pageSize + 1;
                $totalPages = $this->page + 1;
            }
        }

        $sql = $countSQLInMainSelect ? $countSQL : $this->selectSQL;
        if ($this->sortOrder && array_key_exists($this->sortOrder, $this->sortOrders)) {
            if (is_array($this->sortOrders[$this->sortOrder])) {
                $sql .= " ORDER BY " . $this->sortOrders[$this->sortOrder][$this->sortDirection == "ASC" ? 0 : 1];
            } else {
                $sql .= " ORDER BY " . $this->sortOrders[$this->sortOrder] . ($this->sortDirection == "ASC" ? "" : " DESC");
            }
        } elseif ($this->defaultSortOrder) {
            $sql .= " ORDER BY " . $this->defaultSortOrder;
        }
        if ($totalPages == 0) {
            $this->page = 1;
        } elseif ($this->page > $totalPages) {
            $this->page = $totalPages;
        }
        if ($pagingEnabled) {
            $sql .= " LIMIT " . (($this->page - 1) * $this->pageSize) . ", " . $this->pageSize;
        }
        $vars["rows"] = $this->db->fetchAll($sql);
        if (!$pagingEnabled) {
            $totalRows = count($vars["rows"]);
            $totalPages = ceil($totalRows / $this->pageSize);
        } elseif ($countSQLInMainSelect) {
            $totalRows = $this->db->fetchOne("SELECT  FOUND_ROWS()");
            $totalPages = ceil($totalRows / $this->pageSize);
        }

        $vars["totalRows"] = $totalRows;
        $vars["totalPages"] = $totalPages;

        //set URI for subcomponents(navigator, sorter)
        $params = $this->request->getParams();
        //remove empty variables
        foreach ($params as $name => $value) {
            if (empty($value) && $value !== "0") {
                unset($params[$name]);
            }
        }

        $params['controller'] = $this->request->getControllerName();
        $params['action'] = $this->request->getActionName();
        if (array_key_exists($this->name . 'Page', $params)) {
            unset($params[$this->name . 'Page']);
        }
        $vars["pagerURI"] = $this->actionController->getHelper('Url')->url(
                        $params,
                        null,
                        true
                ) . '/';
        //remove sorter parameters
        if (array_key_exists($this->name . 'Order', $params)) {
            unset($params[$this->name . 'Order']);
        }
        if (array_key_exists($this->name . 'Dir', $params)) {
            unset($params[$this->name . 'Dir']);
        }

        $vars["sorterURI"] = $this->actionController->getHelper('Url')->url(
                        $params,
                        null,
                        true
                ) . '/';

        $vars["page"] = $this->page;
        $vars["pageSize"] = $this->pageSize;
        $vars["sortOrder"] = $this->sortOrder;
        $vars["sortDirection"] = $this->sortDirection;
        $this->view->{$this->name} = $vars;
    }

    /**
     * Set DB connection link
     * @param Zend_Db_Adapter_Abstract $db Database link
     * @return Zend_Db_Adapter_Abstract
     */
    public function setDb(Zend_Db_Adapter_Abstract $db) {
        $this->db = $db;
    }

    /**
     * Return DB connection link
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDb() {
        return $this->db;
    }

}

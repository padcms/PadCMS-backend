<?php
/**
 * @file
 * AM_Component_Pager class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Pager component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Pager extends Volcano_Component_Record
{
    /** @var AM_Component_Grid */
    protected $_oComponentGrid = null; /**< @type AM_Component_Grid */

    /**
     * @param AM_Controller_Action     $oActionController
     * @param string $sName
     * @param AM_Component_Grid $oComponentGrid
     */
    public function __construct(AM_Controller_Action $oActionController, $sName, AM_Component_Grid $oComponentGrid)
    {
        $this->_oComponentGrid = $oComponentGrid;
        parent::__construct($oActionController, $sName, array());
    }

    /**
     * Prepeare paginator data for view
     */
    public function show()
    {
        $iTotalRows   = $this->_oComponentGrid->getTotalRows();
        $iTotalPages  = $this->_oComponentGrid->getTotalPages();
        $iCurrentPage = $this->_oComponentGrid->getPage();

        $iStart = $iCurrentPage - round($iTotalRows / 2) + 1;
        $iEnd   = $iCurrentPage + round($iTotalRows / 2) - 1;
        if ($iStart < 1) {
            $iStart = 1;
            $iEnd = $iTotalRows;
            if ($iEnd > $iTotalPages) {
                $iEnd = $iTotalPages;
            }
        } else {
            if ($iEnd > $iTotalPages) {
                $iEnd = $iTotalPages;
                $iStart = $iTotalPages - $iTotalRows;
                if ($iStart < 1) {
                    $iStart = 1;
                }
            }
        }

        if ($iCurrentPage > 1) {
            $iPrevPage = $iCurrentPage - 1;
        } else {
            $iPrevPage = $iCurrentPage;
        }

        if ($iCurrentPage < $iTotalPages) {
            $iNextPage = $iCurrentPage + 1;
        } else {
            $iNextPage = $iCurrentPage;
        }

        $aNearestPages = array();
        for($i = $iStart ; $i <= $iEnd; $i++) {
            if ($i >= 1) {
                if ($i == $iCurrentPage) {
                    $aNearestPages[] = array('page' => $i, 'isCurrentPage' => true);
                } else {
                    $aNearestPages[] = array('page' => $i, 'isCurrentPage' => false);
                }
            }
        }


        $aData["gridName"]     = $this->_oComponentGrid->getName();
        $aData['pagerURI']     = $this->_oComponentGrid->getPagerURI();
        $aData['pageSizeURI']  = $this->_oComponentGrid->getPageSizeURI();
        $aData['perPage']      = $this->_oComponentGrid->getPageSize();
        $aData['nearestPages'] = $aNearestPages;
        $aData['prevPage']     = $iPrevPage;
        $aData['currentPage']  = $iCurrentPage;
        $aData['nextPage']     = $iNextPage;
        $aData['totalPages']   = $iTotalPages;

        $this->view->{$this->getName()} = $aData;
    }
}

<?php
/**
 * @file
 * AM_View_Helper_Breadcrumbs class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_View_Helper
 */
class AM_View_Helper_Breadcrumbs extends Zend_View_Helper_Abstract
{
    const CLIENT = 'client';
    const APP    = 'application';
    const ISSUE  = 'issue';
    const REV    = 'revision';
    const PAGE   = 'page';
    const USER   = 'user';

    /** @var AM_Controller_Action_Helper_Smarty */
    public $oView = null; /**< @type AM_Controller_Action_Helper_Smarty */
    /** @var Zend_Db_Adapter_Abstract */
    public $oDbAdapter = null; /**< @type Zend_Db_Adapter_Abstract */

    private $_client_id_request_names      = array('cid', 'client');
    private $_application_id_request_names = array('aid', 'application');
    private $_issue_id_request_names       = array('iid', 'issue');
    private $_revision_id_request_names    = array('rid', 'revision');
    private $_page_id_request_names        = array();
    private $_user_id_request_names        = array('uid');

    /**
     * @param AM_Controller_Action_Helper_Smarty $oView
     * @param type $oDbAdapter
     * @param type $aUser
     * @param type $sType
     * @param type $aOptions
     */
    public function __construct(AM_Controller_Action_Helper_Smarty $oView, Zend_Db_Adapter_Abstract $oDbAdapter, $aUser, $sType, $aOptions)
    {
        $this->oView      = $oView;
        $this->oDbAdapter = $oDbAdapter;
        $this->aUser      = $aUser;
        $this->sName      = 'breadcrumbs';
        $this->sType      = $sType;
        $this->aOptions   = $aOptions;
    }

    /**
     * Detect id in parameters list
     * @param array $aReqVariants
     * @return int | null
     */
    private function _idScan($aReqVariants)
    {
        foreach ($aReqVariants as $sValue) {
            if(isset($this->aOptions[$sValue]) && $this->aOptions[$sValue]) {
                $iId = $this->aOptions[$sValue];
            }
        }

        return isset($iId) ? $iId : null;
    }

    /**
     * @param Zend_Db_Select $oQuery
     * @param string $sPart
     * @param boolean $bLast
     */
    private function _addQueryPart(Zend_Db_Select &$oQuery, $sPart, $bLast)
    {
        if ($sPart != self::CLIENT) {
            $sIdField    = 'id';
            $sTitleField = 'title';

            switch ($sPart) {
                case self::APP      : $sJoinTo = self::CLIENT;   break;
                case self::ISSUE    : $sJoinTo = self::APP;      break;
                case self::REV      : $sJoinTo = self::ISSUE;    break;
                case self::PAGE     : $sJoinTo = self::REV;      break;
                case self::USER     : $sJoinTo = self::CLIENT;
                    $sTitleField = new Zend_Db_Expr('CONCAT(first_name, " ", last_name)');
                    break;
            }

            if ($sJoinTo) {
                $sOn = "$sPart.$sJoinTo = $sJoinTo.$sIdField";

                $aCols = array("{$sPart}_title"  => $sTitleField,
                               "{$sPart}_id"     => $sIdField,
                               "{$sPart}_parent" => "{$sJoinTo}.id");

                $oQuery->joinLeft($sPart, $sOn , $aCols);
            }
        }

        if ($bLast) {
            $sProperyNameWithKeys = '_' . $sPart . '_id_request_names';
            $iId = $this->_idScan($this->$sProperyNameWithKeys);

            if ($iId) {
                $oQuery->where("$sPart.id = ?", $iId);
            } else {
                if (isset($sJoinTo)) {
                    $sProperyNameWithKeys = '_' . $sJoinTo . '_id_request_names';
                    $iId = $this->_idScan($this->$sProperyNameWithKeys);

                    $oQuery->where("$sJoinTo.id = ?", $iId);
                }
            }
        }
    }

    /**
     * @param string $sKey
     * @param array $aData
     * @return void
     */
    private function _makeURL($sKey, &$aData)
    {
        if (isset($aData['id'])) {
            switch ($sKey) {
                case self::CLIENT   : $sPrev = (isset($aData['controller']) ? $aData['controller'] : self::APP);    break;
                case self::APP      : $sPrev = self::ISSUE;  break;
                case self::ISSUE    : $sPrev = self::REV;    break;
                case self::REV      : $sPrev = self::PAGE;   break;
                case self::PAGE     : $sPrev = null;         break;
                case self::USER     : $sPrev = null;         break;
            }

            if (!$sPrev) {
                $aData['url'] = '/';
                return;
            }

            $sProperyNameWithKeys = '_' . $sKey . '_id_request_names';
            $aIdKeys              = $this->{$sProperyNameWithKeys};
            $aData['url'] = "/{$sPrev}/list/{$aIdKeys[0]}/{$aData['id']}";
        }
    }

    /**
     * @param array $aStructure
     */
    private function _makeData(&$aStructure) {

        $oQuery = $this->oDbAdapter->select()
                          ->from('client', array('client_id' => 'id', 'client_title' => 'title'));

        $iRev = 1;

        foreach ($aStructure as $sKey => $mValue) {
            $this->_addQueryPart($oQuery, $sKey, $iRev == count($aStructure) ? true: false);
            $iRev++;
        }

        $aData = $this->oDbAdapter->fetchRow($oQuery);
        foreach ($aData as $sKey => $mValue) {
            $aExpKey =  explode('_', $sKey);

            $aStructure[$aExpKey[0]][$aExpKey[1]] = $mValue;
        }

        foreach ($aStructure as $sKey => &$mValue) {
            $this->_makeURL($sKey, $mValue);

            if (isset($sPrevKey)) {
                $aStructure[$sPrevKey]['next'] = $sKey;
            }
            $sPrevKey = $sKey;
        }
    }

    /**
     * @return array
     */
    private function breadcrumbs() {
        $aBreadcrumbs = array();

        $aBreadcrumbs['client'] = array();

        switch ($this->sType) {
            case self::CLIENT :
                $aBreadcrumbs['client'] = array('action' => $this->aOptions['action']);
                break;
            case self::USER :
                $aBreadcrumbs['client'] = array('controller' => 'user');
                $aBreadcrumbs += array( 'user' => array('action' => $this->aOptions['action']) );
                break;
            case self::APP :
                $aBreadcrumbs += array( 'application' => array('action' => $this->aOptions['action']) );
                break;
            case self::ISSUE :
                $aBreadcrumbs += array('application' => array(),
                                      'issue' => array('action' => $this->aOptions['action'])
                                     );
                break;
            case self::REV :
                $aBreadcrumbs += array('application' => array(),
                                      'issue' => array(),
                                      'revision' => array('action' => $this->aOptions['action']));
                break;
            case self::PAGE :
                $aBreadcrumbs += array('application' => array(),
                                      'issue' => array(),
                                      'revision' => array(),
                                      'page' => array('action' => $this->aOptions['action']));
                $this->aOptions['action'] = 'list';
                break;
            default:
                break;
        }

        $this->_makeData($aBreadcrumbs);

        if ($this->aOptions['action'] == 'list') {
            unset($aBreadcrumbs[$this->sType]);
        }

        return array('user' => $this->aUser, 'breadcrumbs' => $aBreadcrumbs);
    }

    /**
     * Prepares data for view
     */
    public function show()
    {
        $aData = $this->breadcrumbs();

        if (isset($this->oView->{$this->sName})) {
            $aData = array_merge($aData, $this->oView->{$this->sName});
        }

        $this->oView->{$this->sName} = $aData;
    }
}
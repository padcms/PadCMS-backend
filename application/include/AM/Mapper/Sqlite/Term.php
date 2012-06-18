<?php
/**
 * @file
 * AM_Mapper_Sqlite_Term class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Mapper
 */
class AM_Mapper_Sqlite_Term extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_Term
     */
    protected function _unmapCustom()
    {
        $oTerm   = $this->getModel();
        /* @var $oTerm AM_Model_Db_Term */
        $iPageId = null;

        $oPages = $oTerm->getPages();
        if(count($oPages)) {
            $iPageId = $oPages->current()->id;
        }

        $aData = array(
                'id'            => $oTerm->id,
                'title'         => $oTerm->title,
                'description'   => $oTerm->description,
                'thumb_stripe'  => $oTerm->getResources()->getValueForExport(AM_Model_Db_Term_Data_Resource::RESOURCE_KEY_STRIPE),
                'thumb_summary' => $oTerm->getResources()->getValueForExport(AM_Model_Db_Term_Data_Resource::RESOURCE_KEY_SUMMARY),
                'color'         => $oTerm->color,
                'firstpage_id'  => $iPageId
        );
        $this->_getSqliteGateway('menu')->insert($aData);

        return $this;
    }
}
<?php
/**
 * @file
 * AM_Mapper_Sqlite_PageImposition class definition.
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
class AM_Mapper_Sqlite_PageImposition extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_PageImposition
     */
    protected function _unmapCustom()
    {
        $aData = array(
            'id'            => $this->getModel()->id,
            'page_id'       => $this->getModel()->page,
            'is_linked_to'  => $this->getModel()->is_linked_to,
            'position_type' => $this->getModel()->link_type
        );

        $this->_getSqliteGateway()->insert($aData);

        return $this;
    }
}
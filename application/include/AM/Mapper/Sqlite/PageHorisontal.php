<?php
/**
 * @file
 * AM_Mapper_Sqlite_PageHorisontal class definition.
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
class AM_Mapper_Sqlite_PageHorisontal extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_PageHorisontal
     */
    protected function _unmapCustom()
    {
        $aData = array(
            'id'       => $this->getModel()->id,
            'resource' => $this->getModel()->getResourcePathForExport()
        );

        $this->_getSqliteGateway()->insert($aData);

        return $this;
    }
}
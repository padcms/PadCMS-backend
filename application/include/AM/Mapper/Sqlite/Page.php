<?php
/**
 * @file
 * AM_Mapper_Sqlite_Page class definition.
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
class AM_Mapper_Sqlite_Page extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_Page
     */
    protected function _unmapCustom()
    {
        $aData = array(
            'id'                 => $this->getModel()->id,
            'title'              => $this->getModel()->title,
            'horisontal_page_id' => intval($this->getModel()->pdf_page),
            'template'           => intval($this->getModel()->template),
            'machine_name'       => $this->getModel()->machine_name
        );

        $this->_getSqliteGateway()->insert($aData);

        return $this;
    }
}
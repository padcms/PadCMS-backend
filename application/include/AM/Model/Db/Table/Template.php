<?php
/**
 * @file
 * AM_Model_Db_Table_Template class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Model
 */
class AM_Model_Db_Table_Template extends AM_Model_Db_Table_Abstract
{
    /**
     * Get templates vith version <= $version
     * @param int $iVersion
     * @return AM_Model_Db_Rowset_Template
     */
    public function findAllByVersion($iVersion)
    {
        $iVersion = intval($iVersion);

        $oQuery = $this->select()
                ->where('engine_version <= ?', $iVersion)
                ->order(array('weight ASC'));

        $oTemplates = $this->fetchAll($oQuery);

        return $oTemplates;
    }
}
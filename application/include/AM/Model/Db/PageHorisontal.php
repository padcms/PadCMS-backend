<?php
/**
 * @file
 * AM_Model_Db_PageHorisontal class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Page horisontal model class
 * @ingroup AM_Model
 */
class AM_Model_Db_PageHorisontal extends AM_Model_Db_Abstract
{
    const RESOURCE_TYPE = 'cache-static-pdf';

    /**
     * Get resource path for export
     *
     * @return mixed
     */
    public function getResourcePathForExport()
    {
        if (empty($this->resource)) {
            return false;
        }

        $sValue = '/' . self::RESOURCE_TYPE
                . '/' . AM_Tools_String::generatePathFromId($this->id_issue)
                . '/' . $this->resource;

        return $sValue;
    }
}
<?php
/**
 * @file
 * AM_Application_Resource_Db class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for creating database adapter
 * @ingroup AM_Application
 */
class AM_Application_Resource_Db extends Zend_Application_Resource_Db
{
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Db_Adapter_Abstract|null
     */
    public function init()
    {
        if (null !== ($oDbAdapter = $this->getDbAdapter())) {
            if ($this->isDefaultTableAdapter()) {
                Zend_Db_Table::setDefaultAdapter($oDbAdapter);
            }

            $oDbAdapter->query('SET NAMES utf8');

            return $oDbAdapter;
        }
    }
}

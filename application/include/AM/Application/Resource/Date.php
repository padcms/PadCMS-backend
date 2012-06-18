<?php
/**
 * @file
 * AM_Application_Resource_Date class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Resource for initializing the Zend_Date
 * @ingroup AM_Application
 */
class AM_Application_Resource_Date extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return void
     */
    public function init()
    {
        Zend_Date::setOptions($this->getOptions());
    }
}

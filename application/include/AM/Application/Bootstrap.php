<?php
/**
 * @file
 * AM_Application_Bootstrap class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Application
 */

/**
 * @ingroup AM_Application
 */
class AM_Application_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initAutoloader()
    {
        $oAutoloader = $this->getApplication()->getAutoloader();
        $oAutoloader->suppressNotFoundWarnings('false');
    }

    /**
     * Retrieve resource container
     *
     * @return object
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            $this->setContainer(Zend_Registry::getInstance());
        }
        return $this->_container;
    }
}
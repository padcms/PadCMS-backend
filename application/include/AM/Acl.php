<?php
/**
 * @file
 * AM_Acl class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Acl
 */

/**
 * This class defines base roles, resources and restrictions of the roles
 * @ingroup AM_Acl
 */
class AM_Acl extends Zend_Acl
{

    const RESOURCE_APP               = 'application';
    const RESOURCE_ISSUE             = 'issue';
    const RESOURCE_REV               = 'revision';
    const RESOURCE_PAGE              = 'page';
    const RESOURCE_ELEMENT           = 'element';
    const RESOURCE_USER              = 'user';
    const RESOURCE_TOC               = 'toc';
    const RESOURCE_EXPORT_REVISION   = 'exportRevision';
    const RESOURCE_EXPORT_PAGE       = 'exportPage';
    const RESOURCE_EXPORT_FOR_DEVICE = 'exportForDevice';

    public function __construct()
    {
        /* Roles */
    	$this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('member'), 'guest');
    	$this->addRole(new Zend_Acl_Role('user'),  'member');
    	$this->addRole(new Zend_Acl_Role('admin'), 'member');

        /* Resources */
        $this->add(new Zend_Acl_Resource('index.php'));
        $this->add(new Zend_Acl_Resource('test'));
        $this->add(new Zend_Acl_Resource('admin'));
        $this->add(new Zend_Acl_Resource('auth'));
        $this->add(new Zend_Acl_Resource('error'));
        $this->add(new Zend_Acl_Resource('cron'));
        $this->add(new Zend_Acl_Resource('index'));
        $this->add(new Zend_Acl_Resource('issue'));
        $this->add(new Zend_Acl_Resource('page'));
        $this->add(new Zend_Acl_Resource('revision'));
        $this->add(new Zend_Acl_Resource('client'));
        $this->add(new Zend_Acl_Resource('application'));
        $this->add(new Zend_Acl_Resource('user'));
        $this->add(new Zend_Acl_Resource('editor'));
        $this->add(new Zend_Acl_Resource('java-script'));
        $this->add(new Zend_Acl_Resource('page-map'));
        $this->add(new Zend_Acl_Resource('export'));
        $this->add(new Zend_Acl_Resource('import'));
        $this->add(new Zend_Acl_Resource('statistic'));
        $this->add(new Zend_Acl_Resource('settings'));
        $this->add(new Zend_Acl_Resource('devices'));

        $this->add(new Zend_Acl_Resource('field'));
        $this->add(new Zend_Acl_Resource('field-slide'));
        $this->add(new Zend_Acl_Resource('field-background'));
        $this->add(new Zend_Acl_Resource('field-body'));
        $this->add(new Zend_Acl_Resource('field-gallery'));
        $this->add(new Zend_Acl_Resource('field-mini-art'));
        $this->add(new Zend_Acl_Resource('field-overlay'));
        $this->add(new Zend_Acl_Resource('field-scrolling-pane'));
        $this->add(new Zend_Acl_Resource('field-video'));
        $this->add(new Zend_Acl_Resource('field-sound'));
        $this->add(new Zend_Acl_Resource('field-html'));
        $this->add(new Zend_Acl_Resource('field-advert'));
        $this->add(new Zend_Acl_Resource('field-drag-and-drop'));
        $this->add(new Zend_Acl_Resource('field-popup'));
        $this->add(new Zend_Acl_Resource('field-html5'));
        $this->add(new Zend_Acl_Resource('field-games-crossword'));
        $this->add(new Zend_Acl_Resource('field-3d'));

        /* Restrictions */
        $this->allow(null, 'auth');
        $this->allow(null, 'error');
        $this->allow(null, 'java-script');
        $this->allow(null, 'test');

        $this->allow(null, 'export');
        $this->allow(null, 'import');

        $this->allow('guest', 'application', 'ping');
        $this->allow('guest', 'client', 'get-issues');
        $this->allow('guest', 'devices', 'is-valid');
        $this->allow('guest', 'issue', 'download');

        $this->allow('admin');

        $this->allow('member', 'field');
        $this->allow('member', 'field-slide');
        $this->allow('member', 'field-background');
        $this->allow('member', 'field-body');
        $this->allow('member', 'field-gallery');
        $this->allow('member', 'field-mini-art');
        $this->allow('member', 'field-overlay');
        $this->allow('member', 'field-scrolling-pane');
        $this->allow('member', 'field-video');
        $this->allow('member', 'field-sound');
        $this->allow('member', 'field-html');
        $this->allow('member', 'field-advert');
        $this->allow('member', 'field-popup');
        $this->allow('member', 'field-drag-and-drop');
        $this->allow('member', 'field-html5');
        $this->allow('member', 'field-games-crossword');
        $this->allow('member', 'field-3d');

        $this->allow('member', 'user', 'index');
        $this->allow('member', 'user', 'show');

        $this->allow('member', 'statistic');
        $this->allow('member', 'application');
        $this->allow('member', 'index');
        $this->allow('member', 'editor');
        $this->allow('member', 'issue');
        $this->allow('member', 'page');
        $this->allow('member', 'revision');
        $this->allow('member', 'page-map');
    }
}
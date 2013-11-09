<?php
/**
 * @file
 * AM_Acl class definition.
 *
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
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
        $this->add(new Zend_Acl_Resource('devices'));
        $this->add(new Zend_Acl_Resource('subscription'));

        $this->add(new Zend_Acl_Resource('field'));
        $this->add(new Zend_Acl_Resource('field-slide'));
        $this->add(new Zend_Acl_Resource('field-background'));
        $this->add(new Zend_Acl_Resource('field-body'));
        $this->add(new Zend_Acl_Resource('field-gallery'));
        $this->add(new Zend_Acl_Resource('field-mini-art'));
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

        $this->allow('guest', 'issue', 'download');

        $this->allow('admin');

        $this->allow('member', 'field');
        $this->allow('member', 'field-slide');
        $this->allow('member', 'field-background');
        $this->allow('member', 'field-body');
        $this->allow('member', 'field-gallery');
        $this->allow('member', 'field-mini-art');
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
        $this->allow('member', 'application');
        $this->allow('member', 'index');
        $this->allow('member', 'editor');
        $this->allow('member', 'issue');
        $this->allow('member', 'page');
        $this->allow('member', 'revision');
        $this->allow('member', 'page-map');
        $this->allow('member', 'subscription');
    }
}
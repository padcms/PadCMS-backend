<?php
/**
 * @file
 * Frontend script
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application'));
//Define path to the public directory
define('SITE_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

define('APPLICATION_PREFIX',  'front');

// Ensure include/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'include'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$oApplication = new Zend_Application(
    APPLICATION_PREFIX . '_' . APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$oApplication->bootstrap()
             ->run();

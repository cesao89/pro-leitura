<?php
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// List of servers
// each array will contain all your server paths for a specific stage of application
$testing = array();
$develop = array('127.0.0.1', '192.168.0.116', '192.168.0.221', '192.168.0.109', '::1');

// What is our host right now ?
require_once('getrealip.php');
$host = getUserIP();

// Define application environment
if (in_array($host, $develop)) {
    $typeAccess = 'development';

    define('DB_HOST', getenv('DB_HOST'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASS', getenv('DB_PASS'));
} elseif (in_array($host, $testing)) {
    $typeAccess = 'testing';
} else {
    $typeAccess = 'production';
}

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $typeAccess));

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path()
        )
    )
);

/** Zend_Application */
require_once (APPLICATION_PATH . '/../library/Zend/Application.php');

/** Zend Session */
require_once (APPLICATION_PATH . '/../library/Zend/Session/Namespace.php');

/** Autoload Composer */
if(file_exists(APPLICATION_PATH . '/../vendor/autoload.php'))
    require_once (APPLICATION_PATH . '/../vendor/autoload.php');

/** Constantes do sistema */
require_once (APPLICATION_PATH . '/configs/constants.php');

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();
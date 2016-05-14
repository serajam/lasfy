<?php

$env = 'production';
$env = 'development';
// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $env));

date_default_timezone_set('Europe/Kiev');
setlocale(LC_CTYPE, 'en_EN.UTF-8');
ini_set('iconv.internal_encoding', 'UTF-8');
define('PAGE_LOAD_TIME', 0);
defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__)));

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', BASE_PATH . '/../application');

// define path to users public
defined('USERS_PUBLIC') || define('USERS_PUBLIC', BASE_PATH . '/data/users');


if (APPLICATION_ENV == 'development') {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
}
// creating autoload classmap if no exists
$rootPath = realpath(BASE_PATH . '/../');

if (!file_exists($rootPath . '/library/autoload_classmap.php')) {
    exec('php ' . $rootPath . '/bin/classmap_generator.php -l ' . $rootPath . '/library -w');
}
if (!file_exists($rootPath . '/application/autoload_classmap.php')) {
    exec('php ' . $rootPath . '/bin/classmap_generator.php -l ' . $rootPath . '/application' . ' -w');
}
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

if (!is_dir(APPLICATION_PATH . '/../library') || !is_dir(APPLICATION_PATH . '/../application')){
    die('NO LIBRARY OR APP');
}

require_once APPLICATION_PATH . '/../library/Core/ApplicationLoad.php';
require_once APPLICATION_PATH . '/../library/Core/Error/Handler.php';
require_once APPLICATION_PATH . '/../library/functions.php';

if (PAGE_LOAD_TIME == 1) {
    Core_ApplicationLoad::start();
}


// set error handler
//Core_Error_Handler::set();

// Create application, bootstrap, and run
require_once 'Core/App.php';
$application = new Core_App(APPLICATION_ENV);
$application->bootstrap()->run();

// show log time
if (PAGE_LOAD_TIME == 1) {
    Core_ApplicationLoad::stop();
}

// Verify e-mail sending. Put it in public folder.
<?php

//$env = 'production';
$env = 'development';
// Define application environment
date_default_timezone_set('Europe/Kiev');
setlocale(LC_CTYPE, 'ru_RU.UTF-8');
ini_set('iconv.internal_encoding', 'UTF-8');

define('PAGE_LOAD_TIME', 1);
defined('BASE_PATH') || define('BASE_PATH', realpath(dirname(__FILE__)));

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', BASE_PATH . '/../application');

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $env));

if (APPLICATION_ENV == 'development') {
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
}

// Ensure library/ is on include_path
set_include_path(
    APPLICATION_PATH . '/../library' . PATH_SEPARATOR .
    APPLICATION_PATH . '/../library/Zend' . PATH_SEPARATOR
);

require_once APPLICATION_PATH . '/../library/Zend/Loader/AutoloaderFactory.php';
require_once APPLICATION_PATH . '/../library/functions.php';
Zend_Loader_AutoloaderFactory::factory(
    [
        'Zend_Loader_StandardAutoloader' => [
            'prefixes'            => [
                'Core' => APPLICATION_PATH . '/../library/Core',
                'Zend' => APPLICATION_PATH . '/../library/Zend',
            ],
            'fallback_autoloader' => true,
        ],
    ]
);

/** Zend_Application */
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/config/application.ini'
);

echo 'Begin send e-mail...</br>';

$mail = new Zend_Mail();
$body =
    '<h3>Test script exception</h3>';

$mail->setBodyHtml($body);
$mail->setFrom('server@vliegtickets.nl', 'Website vliegtickets');
$mail->addTo('oleksii.kaharlytskyi@daxx.com', 'alexius');
$mail->setSubject('Send mail TEST');
$res = $mail->send();

echo 'End sending.';
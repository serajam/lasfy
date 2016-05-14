<?php
/**
 * Cache cleaner
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
set_include_path(
    __DIR__ . '/../library' . PATH_SEPARATOR .
    __DIR__ . '/../library/Zend' . PATH_SEPARATOR
);
require_once __DIR__ . '/../library/Zend/Config/Ini.php';
$config = new Zend_Config_Ini(__DIR__ . '/../application/config/application.ini', 'development');
$cacheDir = __DIR__ . '/../application/cache/';
echo 'Cache dir to clear: ' . $cacheDir . PHP_EOL;
echo 'Clearing metadata...' . PHP_EOL;
$csv = glob($cacheDir . '*');
array_map('unlink', $csv);
echo 'Metadata cleared' . PHP_EOL;
/*$memcache = new Memcache();
$memcache->connect($config->memcached->host, $config->memcached->port);
$memcache->flush();
echo 'Memcached flushed' . PHP_EOL . 'Genetaring autload classmap' . PHP_EOL;*/

exec('php classmap_generator.php -l ../library -w', $our, $result);
exec('php classmap_generator.php -l ../application -w', $our, $result);
echo 'Generating autload classmap finished' . PHP_EOL;
print_r($our);
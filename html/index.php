<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

// Static files workaround
$filename = dirname(__FILE__) . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}
$baseDir = dirname(__FILE__) . '/../';

require $baseDir . 'vendor/autoload.php';

$config = require $baseDir . '/config/config.php';

$app = new Baikal\Application($config);
$app['debug'] = true;


require $baseDir . '/config/services.php';
require $baseDir . '/config/routes.php';

$app->run();

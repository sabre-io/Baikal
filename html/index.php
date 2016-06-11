<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

// Static files workaround
$filename = dirname(__FILE__).preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}
$baseDir = dirname(__FILE__) . '/../';
require_once $baseDir . 'vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

require_once($baseDir . '/config/services.php');
require_once($baseDir . '/config/controllers.php');
require_once($baseDir . '/config/routes.php');

$app->run();

<?php

// Baikal application bootstrap

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set("session.cookie_httponly", 1);
ini_set("display_errors", 1);
ini_set("log_errors", 1);

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('<h1>Incomplete installation</h1><p>Ba&iuml;kal dependencies have not been installed. Please, execute "<strong>composer install</strong>" in the folder where you installed Ba&iuml;kal.');
}

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$app = new Baikal\Application($config);

require __DIR__ . '/../config/services.php';
require __DIR__ . '/../config/routes.php';

return $app;

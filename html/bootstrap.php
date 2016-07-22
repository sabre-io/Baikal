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

/**
 * Mapping PHP errors to exceptions.
 *
 * While this is not strictly needed, it makes a lot of sense to do so. If an
 * E_NOTICE or anything appears in your code, this allows SabreDAV to intercept
 * the issue and send a proper response back to the client (HTTP/1.1 500).
 */
function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

$config = require __DIR__ . '/../config/config.php';

$app = new Baikal\Application(['config' => $config]);

require __DIR__ . '/../config/routes.php';

return $app;

<?php

/*

Addressbook/CardDAV server example

This server features CardDAV support

*/

// settings
date_default_timezone_set('Canada/Eastern');

// Make sure this setting is turned on and reflect the root url for your WebDAV server.
// This can be for example the root / or a complete path to your server script
$baseUri = '/';

/* Database */
$pdo = new PDO('sqlite:data/db.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//Mapping PHP errors to exceptions
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Autoloader 
require_once 'lib/Sabre/autoload.php';

// Backends
$authBackend      = new Sabre_DAV_Auth_Backend_PDO($pdo);
$principalBackend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);
$carddavBackend   = new Sabre_CardDAV_Backend_PDO($pdo); 
//$caldavBackend    = new Sabre_CalDAV_Backend_PDO($pdo); 

// Setting up the directory tree // 
$nodes = array(
    new Sabre_DAVACL_PrincipalCollection($principalBackend),
//    new Sabre_CalDAV_CalendarRootNode($authBackend, $caldavBackend),
    new Sabre_CardDAV_AddressBookRoot($principalBackend, $carddavBackend),
);

// The object tree needs in turn to be passed to the server class
$server = new Sabre_DAV_Server($nodes);
$server->setBaseUri($baseUri);

// Plugins 
$server->addPlugin(new Sabre_DAV_Auth_Plugin($authBackend,'SabreDAV'));
$server->addPlugin(new Sabre_DAV_Browser_Plugin());
//$server->addPlugin(new Sabre_CalDAV_Plugin());
$server->addPlugin(new Sabre_CardDAV_Plugin());
$server->addPlugin(new Sabre_DAVACL_Plugin());

// And off we go!
$server->exec();

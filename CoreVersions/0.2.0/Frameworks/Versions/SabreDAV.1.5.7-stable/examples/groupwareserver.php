<?php

/**
 * This server combines both CardDAV and CalDAV functionality into a single 
 * server. It is assumed that the server runs at the root of a HTTP domain (be 
 * that a domainname-based vhost or a specific TCP port.
 *
 * This example also assumes that you're using SQLite and the database has 
 * already been setup (along with the database tables).
 *
 * You may choose to use MySQL instead, just change the PDO connection 
 * statement.
 */

/**
 * UTC or GMT is easy to work with, and usually recommended for any 
 * application.
 */
date_default_timezone_set('UTC');

/**
 * Make sure this setting is turned on and reflect the root url for your WebDAV 
 * server.
 *
 * This can be for example the root / or a complete path to your server script.
 */
$baseUri = '/';

/**
 * Database 
 *
 * Feel free to switch this to MySQL, it will definitely be better for higher 
 * concurrency.
 */
$pdo = new PDO('sqlite:data/db.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

/**
 * Mapping PHP errors to exceptions.
 *
 * While this is not strictly needed, it makes a lot of sense to do so. If an 
 * E_NOTICE or anything appears in your code, this allows SabreDAV to intercept 
 * the issue and send a proper response back to the client (HTTP/1.1 500).
 */
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Autoloader 
require_once 'lib/Sabre/autoload.php';

/**
 * The backends. Yes we do really need all of them.
 *
 * This allows any developer to subclass just any of them and hook into their 
 * own backend systems.
 */
$authBackend      = new Sabre_DAV_Auth_Backend_PDO($pdo);
$principalBackend = new Sabre_DAVACL_PrincipalBackend_PDO($pdo);
$carddavBackend   = new Sabre_CardDAV_Backend_PDO($pdo); 
$caldavBackend    = new Sabre_CalDAV_Backend_PDO($pdo); 

/**
 * The directory tree
 *
 * Basically this is an array which contains the 'top-level' directories in the 
 * WebDAV server.
 */
$nodes = array(
    // /principals
    new Sabre_CalDAV_Principal_Collection($principalBackend),
    // /calendars
    new Sabre_CalDAV_CalendarRootNode($principalBackend, $caldavBackend),
    // /addressbook
    new Sabre_CardDAV_AddressBookRoot($principalBackend, $carddavBackend),
);

// The object tree needs in turn to be passed to the server class
$server = new Sabre_DAV_Server($nodes);
$server->setBaseUri($baseUri);

// Plugins 
$server->addPlugin(new Sabre_DAV_Auth_Plugin($authBackend,'SabreDAV'));
$server->addPlugin(new Sabre_DAV_Browser_Plugin());
$server->addPlugin(new Sabre_CalDAV_Plugin());
$server->addPlugin(new Sabre_CardDAV_Plugin());
$server->addPlugin(new Sabre_DAVACL_Plugin());

// And off we go!
$server->exec();

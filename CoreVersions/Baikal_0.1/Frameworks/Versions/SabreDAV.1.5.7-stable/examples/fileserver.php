<?php

// !!!! Make sure the Sabre directory is in the include_path !!!
// example:
set_include_path('lib/' . PATH_SEPARATOR . get_include_path()); 

/*

This is the best starting point if you're just interested in setting up a fileserver.

Make sure that the 'public' and 'tmpdata' exists, with write permissions
for your server.

*/

// settings
date_default_timezone_set('Canada/Eastern');
$publicDir = 'public';
$tmpDir = 'tmpdata';

// If you want to run the SabreDAV server in a custom location (using mod_rewrite for instance)
// You can override the baseUri here.
// $baseUri = '/';


// Files we need
require_once 'Sabre/autoload.php';

// Create the root node
$root = new Sabre_DAV_FS_Directory($publicDir);

// The rootnode needs in turn to be passed to the server class
$server = new Sabre_DAV_Server($root);

if (isset($baseUri))
    $server->setBaseUri($baseUri);

// Support for LOCK and UNLOCK 
$lockBackend = new Sabre_DAV_Locks_Backend_File($tmpDir . '/locksdb');
$lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);
$server->addPlugin($lockPlugin);

// Support for html frontend
$browser = new Sabre_DAV_Browser_Plugin();
$server->addPlugin($browser);

// Automatically guess (some) contenttypes, based on extesion
$server->addPlugin(new Sabre_DAV_Browser_GuessContentType());

// Authentication backend
$authBackend = new Sabre_DAV_Auth_Backend_File('.htdigest');
$auth = new Sabre_DAV_Auth_Plugin($authBackend,'SabreDAV');
$server->addPlugin($auth);

// Temporary file filter
$tempFF = new Sabre_DAV_TemporaryFileFilterPlugin($tmpDir);
$server->addPlugin($tempFF);

// And off we go!
$server->exec();

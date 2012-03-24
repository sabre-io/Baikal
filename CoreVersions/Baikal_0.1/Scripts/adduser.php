#!/usr/bin/env php
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal.codr.fr
*
*  This script is part of the Baïkal Server project. The Baïkal
*  Server project is free software; you can redistribute it
*  and/or modify it under the terms of the GNU General Public
*  License as published by the Free Software Foundation; either
*  version 2 of the License, or (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

define("BAIKAL_CONTEXT", TRUE);
define("BAIKAL_CONTEXT_CLI", TRUE);

# Bootstraping Baikal
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/Core/Frameworks/Baikal/Core/Bootstrap.php");	# ../../../

$sUsername = @trim($argv[1]);

if($sUsername === "") {
	die("You have to provide a username; aborting.\n");
}

$sPassword = \Baikal\Core\Tools::bashPromptSilent("Password: ");
$sPasswordConfirm = \Baikal\Core\Tools::bashPromptSilent("Confirm password: ");

if($sPassword === "") {
	die("Password cannot be empty.\n");
}

if($sPassword !== $sPasswordConfirm) {
	die("Passwords don't match; aborting.\n");
}

$sHash = md5($sUsername . ':' . BAIKAL_AUTH_REALM . ':' . $sPassword);

$sEmail = \Baikal\Core\Tools::bashPrompt("Email: ");
$sDisplayName = \Baikal\Core\Tools::bashPrompt("Display name: ");

try {
	$stmt = $pdo->prepare("INSERT INTO users (username, digesta1) VALUES (?, ?)");
	$stmt->bindParam(1, $sUsername);
	$stmt->bindParam(2, $sHash);
	$stmt->execute();
	
	$sPath = 'principals/' . $sUsername;
	$stmt = $pdo->prepare("INSERT INTO calendars (principaluri, displayname, uri, description, components, ctag) VALUES (?,'default calendar','default','','VEVENT,VTODO','1')");
	$stmt->bindParam(1, $sPath);
	$stmt->execute();
	
	# INSERT INTO principals (uri,email,displayname) VALUES ('principals/admin', 'admin@example.org','Adminstrator');
	
	$sPath = 'principals/' . $sUsername;
	$stmt = $pdo->prepare("INSERT INTO principals (uri,email,displayname) VALUES (?, ?, ?)");
	$stmt->bindParam(1, $sPath);
	$stmt->bindParam(2, $sEmail);
	$stmt->bindParam(3, $sDisplayName);
	$stmt->execute();
	
	# INSERT INTO principals (uri,email,displayname) VALUES ('principals/admin/calendar-proxy-read', null, null);
	$sPath = 'principals/' . $sUsername . "/calendar-proxy-read";
	$mNull = null;
	$stmt->bindParam(1, $sPath);
	$stmt->bindParam(2, $mNull);
	$stmt->bindParam(3, $mNull);
	$stmt->execute();
	
	# INSERT INTO principals (uri,email,displayname) VALUES ('principals/admin/calendar-proxy-write', null, null);
	$sPath = 'principals/' . $sUsername . "/calendar-proxy-write";
	$stmt->bindParam(1, $sPath);
	$stmt->bindParam(2, $mNull);
	$stmt->bindParam(3, $mNull);
	$stmt->execute();
	
	# INSERT INTO addressbooks (principaluri, displayname, uri, description, ctag) VALUES ('principals/admin','default calendar','default','','1');
	$sPath = 'principals/' . $sUsername;
	$stmt = $pdo->prepare("INSERT INTO addressbooks (principaluri, displayname, uri, description, ctag) VALUES (?,'default addressbook','default','','1')");
	$stmt->bindParam(1, $sPath);
	$stmt->execute();
	
	echo "User has been added.\n";
	exit(0);
} catch(PDOException $e) {
	echo "Fatal error. User has not been added. Details follow.\n";
	die($e->getMessage());
}
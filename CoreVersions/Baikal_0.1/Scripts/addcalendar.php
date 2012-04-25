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

$pdo = $GLOBALS["DB"]->getPDO();

$sUsername = @trim($argv[1]);

if($sUsername === "") {
	die("You have to provide a username; aborting.\n");
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
$stmt->bindParam(1, $sUsername);
$stmt->execute();
if(($user = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_FIRST)) === FALSE) {
	die("User not found; aborting.\n");
}

$sCalendarID = \Baikal\Core\Tools::bashPrompt("Calendar Key (a unique, lower-case, alphanum token, like 'perso' or 'sailing'): ");
if($sCalendarID === "") {
	die("Calendar Key cannot be empty.\n");
}

$sCalendarID = strtolower($sCalendarID);

if(!preg_match("/[a-zA-Z0-9]+/", $sCalendarID)) {
	die("Calendar Key should contain only letters and numbers.\n");
}

# Fetching calendar
$sPrincipalUri = 'principals/' . $sUsername;
$stmt = $pdo->prepare("SELECT * FROM calendars where LOWER(principaluri)=LOWER(?) AND LOWER(uri)=LOWER(?)");
$stmt->bindParam(1, $sPrincipalUri);
$stmt->bindParam(2, $sCalendarID);
$stmt->execute();
if(($cal = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_FIRST)) !== FALSE) {
	die("This Calendar Key is already in use for this user; aborting.\n");
}

$sCalendarName = \Baikal\Core\Tools::bashPrompt("Calendar Display Name: ");
if($sCalendarName === "") {
	die("Calendar Display Name cannot be empty.\n");
}

try {

	$stmt = $pdo->prepare("INSERT INTO calendars (principaluri, displayname, uri, description, components, ctag) VALUES (?,?,?,'','VEVENT,VTODO','1')");
	$stmt->bindParam(1, $sPrincipalUri);
	$stmt->bindParam(2, $sCalendarName);
	$stmt->bindParam(3, $sCalendarID);
	$stmt->execute();

	echo "Calendar has been added.\n";
	exit(0);
} catch(PDOException $e) {
	echo "Fatal error. Calendar has not been added. Details follow.\n";
	die($e->getMessage());
}
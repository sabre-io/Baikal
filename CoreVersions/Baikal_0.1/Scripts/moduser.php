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

$sUsername = isset($argv[1]) ? trim($argv[1]) : "";

if($sUsername === "") {
	die("You have to provide a username; aborting.\n");
}

# Fetching user
$stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
$stmt->bindParam(1, $sUsername);
$stmt->execute();
if(($user = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_FIRST)) === FALSE) {
	die("User not found; aborting.\n");
}

# Fetching principal
$sUri = "principals/" . $sUsername;
$stmt = $pdo->prepare("SELECT * FROM principals WHERE uri=?");
$stmt->bindParam(1, $sUri);
$stmt->execute();
if(($principal = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_FIRST)) === FALSE) {
	die("Principal not found for user; aborting.\n");
}

echo "--User found--\nid:\t\t" . $user["id"] . "\nemail:\t\t" . $principal["email"] . "\ndisplayname:\t" . $principal["displayname"] . "\n";

echo "\n--Please enter new values--\n";

$sPassword = \Baikal\Core\Tools::bashPromptSilent("Password (empty to leave untouched): ");
$sHash = "";
$sEmail = "";
$sDisplayName = "";

if($sPassword !== "") {
	$sPasswordConfirm = \Baikal\Core\Tools::bashPromptSilent("Confirm password: ");
	if($sPassword !== $sPasswordConfirm) {
		die("Passwords don't match; aborting.\n");
	}
	
	$sHash = md5($sUsername . ':' . BAIKAL_AUTH_REALM . ':' . $sPassword);
}

$sEmail = \Baikal\Core\Tools::bashPrompt("Email (empty to leave untouched): ");
$sDisplayName = \Baikal\Core\Tools::bashPrompt("Display name (empty to leave untouched): ");


if($sHash === "" && $sEmail === "" && $sDisplayName === "") {
	echo ("\nNothing to do. User is left untouched.\n");
	exit(0);
}

if($sHash !== "") {
	$stmt = $pdo->prepare("UPDATE users set digesta1=? WHERE id=?");
	$stmt->bindParam(1, $sHash);
	$stmt->bindParam(2, $user["id"]);
	$stmt->execute();
}

if($sEmail !== "") {
	$stmt = $pdo->prepare("UPDATE principals set email=? WHERE id=?");
	$stmt->bindParam(1, $sEmail);
	$stmt->bindParam(2, $principal["id"]);
	$stmt->execute();
}

if($sDisplayName !== "") {
	$stmt = $pdo->prepare("UPDATE principals set displayname=? WHERE id=?");
	$stmt->bindParam(1, $sDisplayName);
	$stmt->bindParam(2, $principal["id"]);
	$stmt->execute();
}

echo ("\nUser is updated.\n");
exit(0);
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

define("PATH_ENTRYDIR", dirname(__FILE__) . "/");
require_once(PATH_ENTRYDIR . "../Bootstrap.php");
require_once(BAIKAL_PATH_WWWROOT . "classes/BaikalTools.php");
require_once(BAIKAL_PATH_WWWROOT . "classes/BaikalAdmin.php");

$sConfigFile = BAIKAL_PATH_SPECIFIC . "config.php";

if(!file_exists($sConfigFile)) {
	die("Specific/config.php is does not exist. Aborting, cannot modify admin password.");
}

if(!is_writable($sConfigFile)) {
	die("Specific/config.php is not writable. Aborting, cannot modify admin password.");
}

$bFound = FALSE;

if(!defined("BAIKAL_ADMIN_PASSWORDHASH")) {
	echo "-- Info: There's currently no admin password set. --\n";
} else {
	echo "-- Info: The current admin password hash is '" . BAIKAL_ADMIN_PASSWORDHASH . "'. --\n";
	$bFound = TRUE;
}

$sPassword = BaikalTools::bashPromptSilent("New admin password: ");
$sPasswordConfirm = BaikalTools::bashPromptSilent("Confirm new admin password: ");

if($sPassword === "") {
	die("Password cannot be empty.\n");
}

if($sPassword !== $sPasswordConfirm) {
	die("Passwords don't match; aborting.\n");
}

$sHash = BaikalAdmin::hashAdminPassword($sPassword);

echo ("\nNew password hash:" . $sHash . "\n");
$sFileContents = file_get_contents($sConfigFile);

if($bFound === FALSE) {
	$sFileContents .= "\n\n# Baïkal Web interface admin password hash; Set by Core/Scripts/adminpassword.php\ndefine(\"BAIKAL_ADMIN_PASSWORDHASH\", \"" . $sHash . "\");\n";
} else {
	die("TODO: implement update using regex");
}

file_put_contents($sConfigFile, $sFileContents);
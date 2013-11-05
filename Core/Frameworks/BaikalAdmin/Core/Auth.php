<?php
#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://baikal-server.com
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace BaikalAdmin\Core;

class Auth {
	public static function assertEnabled() {
		if(!defined("BAIKAL_ADMIN_ENABLED") || BAIKAL_ADMIN_ENABLED !== TRUE) {
			die("<h1>Ba&iuml;kal Admin is disabled.</h1>To enable it, set BAIKAL_ADMIN_ENABLED to TRUE in <b>Specific/config.php</b>");
		}
		
		self::assertUnlocked();
	}
	
	public static function assertUnlocked() {

		if(defined("BAIKAL_CONTEXT_INSTALL") && BAIKAL_CONTEXT_INSTALL === TRUE) {
			$sToolName = "Ba&iuml;kal Install Tool";
			$sFileName = "ENABLE_INSTALL";
		} else {
			if(!defined("BAIKAL_ADMIN_AUTOLOCKENABLED") || BAIKAL_ADMIN_AUTOLOCKENABLED === FALSE) {
				return TRUE;
			}

			$sToolName = "Ba&iuml;kal Admin";
			$sFileName = "ENABLE_ADMIN";
		}

		$sEnableFile = PROJECT_PATH_SPECIFIC . $sFileName;
		
		$bLocked = TRUE;
		if(file_exists($sEnableFile)) {

			clearstatcache();
			$iTime = intval(filemtime($sEnableFile));
			if((time() - $iTime) < 3600) {
				# file has been created/updated less than an hour ago; update it's mtime
				if(is_writable($sEnableFile)) {
					@file_put_contents($sEnableFile, '');
				}
				$bLocked = FALSE;
			} else {
				// file has been created more than an hour ago
				// delete and declare locked
				if(!@unlink($sEnableFile)) {
					die("<h1>" . $sToolName . " is locked.</h1>To unlock it, create (or re-create if it exists already) an empty file named <strong>" . $sFileName . "</strong> (uppercase, no file extension) in the <b>Specific/</b> folder of Ba&iuml;kal.");
				}
			}
		}

		if($bLocked) {
			die("<h1>" . $sToolName . " is locked.</h1>To unlock it, create (or re-create if it exists already) an empty file named <strong>" . $sFileName . "</strong> (uppercase, no file extension) in the <b>Specific/</b> folder of Ba&iuml;kal.");
		}
	}
	
	public static function isAuthenticated() {
		if(isset($_SESSION["baikaladminauth"]) && $_SESSION["baikaladminauth"] === md5(BAIKAL_ADMIN_PASSWORDHASH)) {
			return TRUE;
		}
		
		return FALSE;		
	}
	
	public static function authenticate() {
		
		if(intval(\Flake\Util\Tools::POST("auth")) !== 1) {
			return FALSE;
		}
		
		$sUser = \Flake\Util\Tools::POST("login");
		$sPass = \Flake\Util\Tools::POST("password");
		
		$sPassHash = self::hashAdminPassword($sPass);
		
		if($sUser === "admin" && $sPassHash === BAIKAL_ADMIN_PASSWORDHASH) {
			$_SESSION["baikaladminauth"] = md5(BAIKAL_ADMIN_PASSWORDHASH);
			return TRUE;
		}

		return FALSE;
		
	}
	
	public static function unAuthenticate() {
		unset($_SESSION["baikaladminauth"]);
	}

	public static function hashAdminPassword($sPassword) {
		if(defined("BAIKAL_AUTH_REALM")) {
			$sAuthRealm = BAIKAL_AUTH_REALM;
		} else {
			$sAuthRealm = "BaikalDAV";	# Fallback to default value; useful when initializing App, as all constants are not set yet
		}

		return md5('admin:' . $sAuthRealm . ':' . $sPassword);
	}

	public static function lockAdmin() {
		@unlink(PROJECT_PATH_SPECIFIC . "ENABLE_ADMIN");
	}

	public static function lockInstall() {
		@unlink(PROJECT_PATH_SPECIFIC . "ENABLE_INSTALL");
	}
}

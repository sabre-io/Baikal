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

require_once(BAIKAL_PATH_FRAMEWORKS . "Tabulator/Tabulator.html.php");

class BaikalAdmin {
	
	static function assertEnabled() {
		if(!defined("BAIKAL_ADMIN_ENABLED") || BAIKAL_ADMIN_ENABLED !== TRUE) {
			die("<h1>Ba&iuml;kal Admin is disabled.</h1>To enable it, set BAIKAL_ADMIN_ENABLED to TRUE in <b>Specific/config.php</b>");
		}
		
		$bLocked = TRUE;
		$sEnableFile = BAIKAL_PATH_SPECIFIC . "ENABLE_ADMIN";
		if(file_exists($sEnableFile)) {
			
			clearstatcache();
			$iTime = intval(filemtime($sEnableFile));
			if((time() - $iTime) < 3600) {
				// file has been created more than an hour ago
				// delete and declare locked
				
				$bLocked = FALSE;
			} else {
				if(!@unlink($sEnableFile)) {
					die("<h1>Ba&iuml;kal Admin is locked.</h1>To unlock it, delete and re-create an empty file named ENABLE_ADMIN in <b>Specific/config.php</b>");
				}
			}
		}
		
		if($bLocked) {
			die("<h1>Ba&iuml;kal Admin is locked.</h1>To unlock it, create an empty file named ENABLE_ADMIN in <b>Specific/</b>");
		} else {
			// update filemtime
			@touch($sEnableFile);
		}
	}
	
	static function displayUsers() {
		$aUsers = BaikalTools::getUsers();
		
		$oTabulator = new TabulatorHtml();
		$oTabulator->addColumn(self::makeColumn("id", "Id", "numeric"));
		$oTabulator->addColumn(self::makeColumn("username", "Username"));
		$oTabulator->render($aUsers);
	}
	
	static function &makeColumn($sName, $sHeader = "", $sType = "text") {
		$oColumn = new TabulatorColumnHtml($sName, $sHeader, $sType);
		return $oColumn;
	}
}
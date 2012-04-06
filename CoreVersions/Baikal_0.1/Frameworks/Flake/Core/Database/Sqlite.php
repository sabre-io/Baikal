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

namespace Flake\Core\Database;

class Sqlite extends \Flake\Core\Database {

	var $oDb = FALSE;	// current DB link
	var $debugOutput = FALSE;
	var $store_lastBuiltQuery = TRUE;
	var $debug_lastBuiltQuery = "";
	var $sDbPath = "";

	function init($sDbPath) {
		if(is_object($this->oDb)) {
			$this->messageAndDie("DB already initialized");
		}
		
		$this->sDbPath = $sDbPath;
		$this->oDb = new \SQLite3($this->sDbPath);
	}
	
	function query($sSql) {
		return $this->oDb->query($sSql);
	}

	function fetch($rSql) {
		if(is_object($rSql)) {
			return $rSql->fetchArray(SQLITE3_ASSOC);
		}
		
		return FALSE;
	}
	
	function sql_insert_id() {
		$rSql = $this->query("SELECT last_insert_rowid() as uid");
		if(($aRes = $this->fetch($rSql)) !== FALSE) {
			return intval($aRes["uid"]);
		}
		
		return FALSE;
	}

	function quoteStr($str, $table=FALSE) {
		if(function_exists("sqlite_escape_string")) {
			return sqlite_escape_string($str);
		}
		
		return addslashes($str);
	}
}
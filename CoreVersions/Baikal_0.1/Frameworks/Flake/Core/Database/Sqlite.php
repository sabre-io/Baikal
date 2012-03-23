<?php

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
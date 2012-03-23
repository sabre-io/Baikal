<?php

namespace Flake\Core;

class Mysql extends \Flake\Core\Database {

	var $link = FALSE;	// current DB link
	var $debugOutput = FALSE;
	var $store_lastBuiltQuery = TRUE;
	var $debug_lastBuiltQuery = "";
	
	/* fonctions abstraites */
	
	function init($sHost = DB_host, $sLogin = DB_login, $sPassword = DB_password, $sDatabase = DB_database) {
		if(is_resource($this->link)) {
			$this->messageAndDie("DB already initialized");
		}

		$this->link = mysql_connect(
			$sHost,
			$sLogin,
			$sPassword
		) OR $this->messageAndDie("invalid DB credentials.");

		mysql_select_db($sDatabase, $this->link) OR $this->messageAndDie("could not select DB");
		
		// on initialise la connection UTF-8 aux données
		mysql_query("set character_set_database='utf8'", $this->link);
		mysql_query("set character_set_client='utf8'", $this->link);
		mysql_query("set character_set_connection='utf8'", $this->link);
		mysql_query("set character_set_results='utf8'", $this->link);
		mysql_query("set character_set_server='utf8'", $this->link);
	}
	
	function query($sSql) {
		return mysql_query($sSql, $this->link);
	}

	function fetch($rSql) {
		if(is_resource($rSql)) {
			return mysql_fetch_assoc($rSql);
		}
		
		return FALSE;
	}
	
	function sql_insert_id()	{
		return mysql_insert_id($this->link);
	}

	function quoteStr($str, $table)	{
		return mysql_real_escape_string($str, $this->link);
	}
}
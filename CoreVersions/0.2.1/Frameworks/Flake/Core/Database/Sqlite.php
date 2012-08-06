<?php
#################################################################
#  Copyright notice
#
#  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
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

namespace Flake\Core\Database;

class Sqlite extends \Flake\Core\Database {

	protected $oDb = FALSE;	// current DB link
	protected $debugOutput = FALSE;
	protected $store_lastBuiltQuery = TRUE;
	protected $debug_lastBuiltQuery = "";
	protected $sDbPath = "";

	public function __construct($sDbPath) {
		$this->sDbPath = $sDbPath;
		$this->oDb = new \PDO('sqlite:' . $this->sDbPath);
#		$this->oDb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
	
	public function query($sSql) {
		if(($stmt = $this->oDb->query($sSql)) === FALSE) {
			$sMessage = print_r($this->oDb->errorInfo(), TRUE);
			throw new \Exception("SQL ERROR in: '" . $sSql . "'; Message: " . $sMessage);
		}
		
		return new \Flake\Core\Database\SqliteStatement($stmt);
	}
	
	public function lastInsertId() {
		return $this->oDb->lastInsertId();
	}

	public function quote($str) {
		return substr($this->oDb->quote($str), 1, -1);	# stripping first and last quote
	}
	
	public function getPDO() {
		return $this->oDb;
	}
}

Class SqliteStatement {
	
	protected $stmt = null;
	
	public function __construct($stmt) {
		$this->stmt = $stmt;
	}
	
	public function fetch() {
		if($this->stmt !== FALSE) {
			return $this->stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_FIRST);
		}
		
		return FALSE;
	}
}
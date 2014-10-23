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

namespace Baikal\Model\Config;

class Database extends \Baikal\Model\Config {
	
	protected $aConstants = array(
		"PROJECT_SQLITE_FILE" => array(
			"type" => "litteral",
			"comment" => "Define path to Baïkal Database SQLite file",
		),
		"PROJECT_DB_MYSQL" => array(
			"type" => "boolean",
			"comment" => "MySQL > Use MySQL instead of SQLite ?",
		),
		"PROJECT_DB_MYSQL_HOST" => array(
			"type" => "string",
			"comment" => "MySQL > Host, including ':portnumber' if port is not the default one (3306)",
		),
		"PROJECT_DB_MYSQL_DBNAME" => array(
			"type" => "string",
			"comment" => "MySQL > Database name",
		),
		"PROJECT_DB_MYSQL_USERNAME" => array(
			"type" => "string",
			"comment" => "MySQL > Username",
		),
		"PROJECT_DB_MYSQL_PASSWORD" => array(
			"type" => "string",
			"comment" => "MySQL > Password",
		),
		"PROJECT_DB_MYSQL_SSL_KEY" => array(
			"type" => "string",
			"comment" => "Path to client SSL key file; leave blank for none",
		),
		"PROJECT_DB_MYSQL_SSL_CERT" => array(
			"type" => "string",
			"comment" => "Path to client SSL certificate; leave blank for none",
		),
		"PROJECT_DB_MYSQL_SSL_CA" => array(
			"type" => "string",
			"comment" => "Path to server CA certificate; set to enable SSL",
		),
	);
	
	# Default values
	protected $aData = array(
		"PROJECT_SQLITE_FILE" => 'PROJECT_PATH_SPECIFIC . "db/db.sqlite"',
		"PROJECT_DB_MYSQL" => FALSE,
		"PROJECT_DB_MYSQL_HOST" => "",
		"PROJECT_DB_MYSQL_DBNAME" => "",
		"PROJECT_DB_MYSQL_USERNAME" => "",
		"PROJECT_DB_MYSQL_PASSWORD" => "",
		"PROJECT_DB_MYSQL_SSL_KEY" => "",
		"PROJECT_DB_MYSQL_SSL_CERT" => "",
		"PROJECT_DB_MYSQL_SSL_CA" => "",
	);
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_SQLITE_FILE",
			"label" => "SQLite file path",
			"validation" => "required",
			"inputclass" => "input-xxlarge",
			"help" => "The absolute server path to the SQLite file",
		)));
		
		$oMorpho->add(new \Formal\Element\Checkbox(array(
			"prop" => "PROJECT_DB_MYSQL",
			"label" => "Use MySQL",
			"help" => "If checked, Baïkal will use MySQL instead of SQLite.",
			"refreshonchange" => TRUE,
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_HOST",
			"label" => "MySQL host",
			"help" => "Host ip or name, including <strong>':portnumber'</strong> if port is not the default one (3306)"
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_DBNAME",
			"label" => "MySQL database name",
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_USERNAME",
			"label" => "MySQL username",
		)));
		
		$oMorpho->add(new \Formal\Element\Password(array(
			"prop" => "PROJECT_DB_MYSQL_PASSWORD",
			"label" => "MySQL password",
		)));

		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_SSL_KEY",
			"label" => "SSL Client Key",
		)));

		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_SSL_CERT",
			"label" => "SSL Client Cert.",
		)));

		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "PROJECT_DB_MYSQL_SSL_CA",
			"label" => "SSL Server CA",
		)));

		return $oMorpho;
	}
		
	public function label() {
		return "Baïkal Database Settings";
	}

	protected static function getDefaultConfig() {
		throw new \Exception("Should never reach getDefaultConfig() on \Baikal\Model\Config\Database");
	}
}

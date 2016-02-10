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

namespace BaikalAdmin\Controller\Install;

class Database extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	protected $oModel;
	protected $oForm;	# \Formal\Form
	
	public function execute() {
		$this->oModel = new \Baikal\Model\Config\Database(PROJECT_PATH_SPECIFIC . "config.system.php");
		
		$this->oForm = $this->oModel->formForThisModelInstance(array(
			"close" => FALSE,
			"hook.validation" => array($this, "validateConnection"),
			"hook.morphology" => array($this, "hideMySQLFieldWhenNeeded"),
		));
		
		if($this->oForm->submitted()) {
			$this->oForm->execute();
			
			if($this->oForm->persisted()) {

				# nothing here
			}
		}
	}

	public function render() {
		$sBigIcon = "glyph2x-magic";
		$sBaikalVersion = BAIKAL_VERSION;
		
		$oView = new \BaikalAdmin\View\Install\Database();
		$oView->setData("baikalversion", BAIKAL_VERSION);
		
		if($this->oForm->persisted()) {
			
			\BaikalAdmin\Core\Auth::lockInstall();

			$sMessage = "<p>Baïkal is now installed, and it's database properly configured. <strong>For security reasons, this installation wizard is now disabled.</strong></p>";
			$sMessage . "<p>&nbsp;</p>";
			$sMessage .= "<p><a class='btn btn-success' href='" . PROJECT_URI . "admin/'>Start using Baïkal</a></p>";
			$sForm = "";
		} else {
			$sMessage = "";
			$sForm = $this->oForm->render();
		}
		
		$oView->setData("message", $sMessage);
		$oView->setData("form", $sForm);
		
		return $oView->render();
	}

	public function validateConnection($oForm, $oMorpho) {

		$bMySQLEnabled = $oMorpho->element("PROJECT_DB_MYSQL")->value();

		if($bMySQLEnabled) {
			
			$sHost = $oMorpho->element("PROJECT_DB_MYSQL_HOST")->value();
			$sDbname = $oMorpho->element("PROJECT_DB_MYSQL_DBNAME")->value();
			$sUsername = $oMorpho->element("PROJECT_DB_MYSQL_USERNAME")->value();
			$sPassword = $oMorpho->element("PROJECT_DB_MYSQL_PASSWORD")->value();

			try {
				$oDb = new \Flake\Core\Database\Mysql(
					$sHost,
					$sDbname,
					$sUsername,
					$sPassword
				);

				if(($aMissingTables = \Baikal\Core\Tools::isDBStructurallyComplete($oDb)) !== TRUE) {

					# Checking if all tables are missing
					$aRequiredTables = \Baikal\Core\Tools::getRequiredTablesList();
					if(count($aRequiredTables) !== count($aMissingTables)) {
						$sMessage = "<br /><p><strong>Database is not structurally complete.</strong></p>";
						$sMessage .= "<p>Missing tables are: <strong>" . implode("</strong>, <strong>", $aMissingTables) . "</strong></p>";
						$sMessage .= "<p>You will find the SQL definition of Baïkal tables in this file: <strong>Core/Resources/Db/MySQL/db.sql</strong></p>";
						$sMessage .= "<br /><p>Nothing has been saved. <strong>Please, add these tables to the database before pursuing Baïkal initialization.</strong></p>";

						$oForm->declareError(
							$oMorpho->element("PROJECT_DB_MYSQL"),
							$sMessage
						);
					} else {
						# All tables are missing
						# We add these tables ourselves to the database, to initialize Baïkal
						$sSqlDefinition = file_get_contents(PROJECT_PATH_CORERESOURCES . "Db/MySQL/db.sql");
						$oDb->query($sSqlDefinition);
					}
				}

				return TRUE;
			} catch(\Exception $e) {
				$oForm->declareError(
					$oMorpho->element("PROJECT_DB_MYSQL"),
					"Baïkal was not able to establish a connexion to the MySQL database as configured.<br />MySQL says: " . $e->getMessage()
				);

				$oForm->declareError(
					$oMorpho->element("PROJECT_DB_MYSQL_HOST")
				);

				$oForm->declareError(
					$oMorpho->element("PROJECT_DB_MYSQL_DBNAME")
				);

				$oForm->declareError(
					$oMorpho->element("PROJECT_DB_MYSQL_USERNAME")
				);

				$oForm->declareError(
					$oMorpho->element("PROJECT_DB_MYSQL_PASSWORD")
				);
			}
        } else {
			
			$sFile = $oMorpho->element("PROJECT_SQLITE_FILE")->value();

            try {

                // not sure yet how to better address this
                // yup! this is mental, but even if we don't use eval, effectively these
                // config settings are eval'ed because they are written as raw php files.
                // We'll have to clean this up later.
                $sFile = eval('return ' . $sFile . ';');


				$oDb = new \Flake\Core\Database\Sqlite(
					$sFile
				);

				if(($aMissingTables = \Baikal\Core\Tools::isDBStructurallyComplete($oDb)) !== TRUE) {

					# Checking if all tables are missing
					$aRequiredTables = \Baikal\Core\Tools::getRequiredTablesList();
					if(count($aRequiredTables) !== count($aMissingTables)) {
						$sMessage = "<br /><p><strong>Database is not structurally complete.</strong></p>";
						$sMessage .= "<p>Missing tables are: <strong>" . implode("</strong>, <strong>", $aMissingTables) . "</strong></p>";
						$sMessage .= "<p>You will find the SQL definition of Baïkal tables in this file: <strong>Core/Resources/Db/SQLite/db.sql</strong></p>";
						$sMessage .= "<br /><p>Nothing has been saved. <strong>Please, add these tables to the database before pursuing Baïkal initialization.</strong></p>";

						$oForm->declareError(
							$oMorpho->element("PROJECT_SQLITE_FILE"),
							$sMessage
						);
					} else {
						# All tables are missing
						# We add these tables ourselves to the database, to initialize Baïkal
                        $sSqlDefinition = file_get_contents(PROJECT_PATH_CORERESOURCES . "Db/SQLite/db.sql");
                        foreach(explode(';', $sSqlDefinition) as $query) {
                            if (!trim($query)) continue;
                            $oDb->query($query);
                        }
					}
				}

				return TRUE;
			} catch(\Exception $e) {
				$oForm->declareError(
					$oMorpho->element("PROJECT_SQLITE_FILE"),
					"Baïkal was not able to establish a connexion to the SQLite database as configured.<br />SQLite says: " . $e->getMessage() . (string)$e
				);
			}
            // SQLite
        }
	}

	public function hideMySQLFieldWhenNeeded(\Formal\Form $oForm, \Formal\Form\Morphology $oMorpho) {

		if($oForm->submitted()) {
			$bMySQL = (intval($oForm->postValue("PROJECT_DB_MYSQL")) === 1);
		} else {
			$bMySQL = PROJECT_DB_MYSQL;
		}

		if($bMySQL === TRUE) {
			$oMorpho->remove("PROJECT_SQLITE_FILE");
		} else {
			
			$oMorpho->remove("PROJECT_DB_MYSQL_HOST");
			$oMorpho->remove("PROJECT_DB_MYSQL_DBNAME");
			$oMorpho->remove("PROJECT_DB_MYSQL_USERNAME");
			$oMorpho->remove("PROJECT_DB_MYSQL_PASSWORD");
		}
	}
}

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

namespace BaikalAdmin\Controller\Settings;

class System extends \Flake\Core\Controller {
	
	public function execute() {
		$this->oModel = new \Baikal\Model\Config\System(PROJECT_PATH_SPECIFIC . "config.system.php");
		
		# Assert that config file is writable
		if(!$this->oModel->writable()) {
			throw new \Exception("System config file is not writable;" . __FILE__ . " > " . __LINE__);
		}
		
		$this->oForm = $this->oModel->formForThisModelInstance(array(
			"close" => FALSE,
			"hook.morphology" => array($this, "morphologyHook"),
			"hook.validation" => array($this, "validationHook"),
		));
		
		if($this->oForm->submitted()) {
			$this->oForm->execute();
		}
	}

	public function render() {
		
		$oView = new \BaikalAdmin\View\Settings\System();
		$oView->setData("message", \Formal\Core\Message::notice(
			"Do not change anything on this page unless you really know what you are doing.<br />You might break Baïkal if you misconfigure something here.",
			"Warning !",
			FALSE
		));
		
		$oView->setData("form", $this->oForm->render());
		
		return $oView->render();
	}
	
	public function morphologyHook(\Formal\Form $oForm, \Formal\Form\Morphology $oMorpho) {
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
	
	public function validationHook(\Formal\Form $oForm, \Formal\Form\Morphology $oMorpho) {
		if(intval($oForm->modelInstance()->get("PROJECT_DB_MYSQL")) === 1) {
				
			# We have to check the MySQL connection
			$sHost = $oForm->modelInstance()->get("PROJECT_DB_MYSQL_HOST");
			$sDbName = $oForm->modelInstance()->get("PROJECT_DB_MYSQL_DBNAME");
			$sUsername = $oForm->modelInstance()->get("PROJECT_DB_MYSQL_USERNAME");
			$sPassword = $oForm->modelInstance()->get("PROJECT_DB_MYSQL_PASSWORD");
			
			try {
				$oDB = new \Flake\Core\Database\Mysql(
					$sHost,
					$sDbName,
					$sUsername,
					$sPassword
				);
			} catch(\Exception $e) {
				$sMessage = "<strong>MySQL error:</strong> " . $e->getMessage();
				$sMessage .= "<br /><strong>Nothing has been saved</strong>";
				$oForm->declareError($oMorpho->element("PROJECT_DB_MYSQL_HOST"), $sMessage);
				$oForm->declareError($oMorpho->element("PROJECT_DB_MYSQL_DBNAME"));
				$oForm->declareError($oMorpho->element("PROJECT_DB_MYSQL_USERNAME"));
				$oForm->declareError($oMorpho->element("PROJECT_DB_MYSQL_PASSWORD"));
				return;
			}
			
			if(($aMissingTables = \Baikal\Core\Tools::isDBStructurallyComplete($oDB)) !== TRUE) {
				$sMessage = "<strong>MySQL error:</strong> These tables, required by Baïkal, are missing: <strong>" . implode(", ", $aMissingTables) . "</strong><br />";
				$sMessage .= "You may want create these tables using the file <strong>Core/Resources/Db/MySQL/db.sql</strong>";
				$sMessage .= "<br /><br /><strong>Nothing has been saved</strong>";
				
				$oForm->declareError($oMorpho->element("PROJECT_DB_MYSQL"), $sMessage);
				return;
			}
		}
	}
}
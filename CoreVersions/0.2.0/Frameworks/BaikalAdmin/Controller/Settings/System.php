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

namespace BaikalAdmin\Controller\Settings;

class System extends \Flake\Core\Controller {
	
	public function __construct() {
		parent::__construct();
		$this->oModel = new \Baikal\Model\Config\System(PROJECT_PATH_SPECIFIC . "config.system.php");
		
		# Assert that config file is writable
		if(!$this->oModel->writable()) {
			throw new \Exception("System config file is not writable;" . __FILE__ . " > " . __LINE__);
		}
		
		$this->oForm = $this->oModel->formForThisModelInstance(array(
			"close" => FALSE
		));
	}
		
	public function execute() {
		if($this->oForm->submitted()) {
			$this->oForm->execute();
		}
	}

	public function render() {
		
		$sHeader =<<<FORM
<header class="jumbotron subhead" id="overview">
	<h1><i class="glyph2x-adjust"></i>Baïkal system settings</h1>
</header>
FORM;

		$sMessage = \Formal\Core\Message::notice(
			"Do not change anything on this page unless you really know what you are doing.<br />You might break Baïkal if you misconfigure something here.",
			"Warning !",
			FALSE
		);
		return $sHeader . $sMessage . $this->oForm->render();
	}
}
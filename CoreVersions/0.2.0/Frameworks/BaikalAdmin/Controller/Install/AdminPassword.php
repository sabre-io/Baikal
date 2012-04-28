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

namespace BaikalAdmin\Controller\Install;

class AdminPassword extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	protected $oModel;	# \BaikalAdmin\Model\Install 
	protected $oForm;	# \Formal\Form
	
/*	public function __construct() {
		parent::__construct();
		
		$this->oModel = new \BaikalAdmin\Model\Install();
		
		$this->oForm = $this->oModel->formForThisModelInstance(array(
			"close" => FALSE
		));
	}
	
	public function execute() {
		if($this->oForm->submitted()) {
			$this->oForm->execute();
		}
	}*/
	
	public function execute() {
	}

	public function render() {
		$sBigIcon = \BaikalAdmin\Model\Install::bigicon();
		$sBaikalVersion = BAIKAL_VERSION;

		$sHtml = <<<HTML
<header class="jumbotron subhead" id="overview">
	<h1><i class="{$sBigIcon}"></i>Baïkal maintainance wizard</h1>
	<p class="lead">Maintaining Baïkal <strong>{$sBaikalVersion}</strong></p>
</header>
HTML;

		$sHtml .= <<<HTML
<p>You have to set a password for the <strong>admin</strong> user.</p>
HTML;

		return $sHtml;
	}
}
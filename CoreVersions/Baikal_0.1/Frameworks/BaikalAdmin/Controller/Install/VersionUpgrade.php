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

class VersionUpgrade extends \Flake\Core\Controller {
	
	protected $aMessages = array();
	protected $oModel;
	protected $oForm;	# \Formal\Form
	
	public function execute() {
	}

	public function render() {
		$sBigIcon = \BaikalAdmin\Model\Install::bigicon();
		$sBaikalVersion = BAIKAL_VERSION;
		$sBaikalConfiguredVersion = BAIKAL_CONFIGURED_VERSION;

		$sHtml = <<<HTML
<header class="jumbotron subhead" id="overview">
	<h1><i class="{$sBigIcon}"></i>Baïkal upgrade wizard</h1>
	<p class="lead">Upgrading Baïkal from version <strong>{$sBaikalConfiguredVersion}</strong> to version <strong>{$sBaikalVersion}</strong></p>
</header>
HTML;

/*		$sHtml .= <<<HTML
<h2>What is this ?</h2>
<p>
	This is the Baïkal Install Tool.<br />
	It's displayed because you just installed or upgraded your Baïkal installation.<br />
	<strong>Baïkal requires some maintenance in order to ensure everything works as expected.</strong>
</p>
HTML;
*/
		return $sHtml;
	}
}
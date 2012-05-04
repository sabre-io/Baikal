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

namespace BaikalAdmin\Controller;

class Login extends \Flake\Core\Controller {
	
	public function execute() {
	}
	
	public function render() {
		$sActionUrl = \Flake\Util\Tools::getCurrentUrl();
		$sSubmittedFlagName = "auth";
		$sLogin = htmlspecialchars(\Flake\Util\Tools::POST("login"));
		$sPassword = htmlspecialchars(\Flake\Util\Tools::POST("password"));
		if(trim($sLogin) === "") {
			$sLogin = "admin";
		}

		$sForm =<<<FORM

<header class="jumbotron subhead" id="overview">
	<h1><i class="glyph2x-lock"></i>Authentication</h1>
		<p class="lead">Please authenticate to access Baïkal Web Admin.</p>
</header>

<form class="form-horizontal" action="{$sActionUrl}" method="post" enctype="multipart/formdata">
	<input type="hidden" name="{$sSubmittedFlagName}" value="1" />
	<fieldset>
		<p>
			<label for="login">Login</label>
			<input type="text" name="login" value="{$sLogin}" />
		</p>

		<p>
			<label for="password">Password</label>
			<input type="password" name="password" value="{$sPassword}" />
		</p>
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Authenticate</button>
		</div>
	</fieldset>
</form>
FORM;
		return $sForm;
	}
}
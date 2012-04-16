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

namespace BaikalAdmin\Controller\Navigation;

class Bar extends \Flake\Core\Controller {

	public function execute() {
	}

	public function render() {
		
		$sCurrentRoute = $GLOBALS["ROUTER"]::getCurrentRoute();
		$sActiveHome = $sActiveUsers = $sActiveSettings = "";
		
		$sControllerForDefaultRoute = $GLOBALS["ROUTER"]::getControllerForRoute("default");
		$sHomeLink = $sControllerForDefaultRoute::link();
		$sUsersLink = \BaikalAdmin\Controller\Users::link();
		$sSettingsLink = \BaikalAdmin\Controller\Settings::link();
		
		if($sCurrentRoute === "default") {
			$sActiveHome = "active";
		}
		if(
			$sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\Users") ||
			$sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\User\Calendars") ||
			$sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\User\AddressBooks")
		) {
			$sActiveUsers = "active";
		}
		
		if($sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\Settings")) {
			$sActiveSettings = "active";
		}
		
		$sHtml =<<<HTML
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="{$sHomeLink}">Baïkal Admin !!!!!</a>
					<div class="nav-collapse">
						<ul class="nav">
							<li class="{$sActiveHome}"> <a href="{$sHomeLink}">Home</a></li>
							<li class="{$sActiveUsers}"> <a href="{$sUsersLink}">Users and resources</a></li>
							<li class="{$sActiveSettings}"> <a href="{$sSettingsLink}">Settings</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>
HTML;
		return $sHtml;
	}
}
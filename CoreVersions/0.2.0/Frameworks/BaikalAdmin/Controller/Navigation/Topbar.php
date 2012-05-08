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

class Topbar extends \Flake\Core\Controller {

	public function execute() {
	}

	public function render() {
		
		$oView = new \BaikalAdmin\View\Navigation\Topbar();
		
		$sCurrentRoute = $GLOBALS["ROUTER"]::getCurrentRoute();
		$sActiveHome = $sActiveUsers = $sActiveSettingsStandard = $sActiveSettingsSystem = "";
		
		$sControllerForDefaultRoute = $GLOBALS["ROUTER"]::getControllerForRoute("default");
		$sHomeLink = $sControllerForDefaultRoute::link();
		$sUsersLink = \BaikalAdmin\Controller\Users::link();
		$sSettingsStandardLink = \BaikalAdmin\Controller\Settings\Standard::link();
		$sSettingsSystemLink = \BaikalAdmin\Controller\Settings\System::link();
		$sLogoutLink = \BaikalAdmin\Controller\Logout::link();
		
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
		
		if($sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\Settings\Standard")) {
			$sActiveSettingsStandard = "active";
		}
		
		if($sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\Settings\System")) {
			$sActiveSettingsSystem = "active";
		}
		
<<<<<<< HEAD
		$sHtml =<<<HTML
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="{$sHomeLink}"><img style="vertical-align: text-top; line-height: 20px;" src="res/core/BaikalAdmin/Templates/Page/baikal-text-20.png" /> Web Admin</a>
					<div class="nav-collapse">
						<ul class="nav">
							<li class="{$sActiveHome}"> <a href="{$sHomeLink}">Dashboard</a></li>
							<li class="{$sActiveUsers}"> <a href="{$sUsersLink}">Users and resources</a></li>
							<li class="{$sActiveSettingsStandard}"> <a href="{$sSettingsStandardLink}">Settings</a></li>
							<li class="{$sActiveSettingsSystem}"> <a href="{$sSettingsSystemLink}">System settings</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>
HTML;
		return $sHtml;
=======
		$oView->setData("activehome", $sActiveHome);
		$oView->setData("activeusers", $sActiveUsers);
		$oView->setData("activesettingsstandard", $sActiveSettingsStandard);
		$oView->setData("activesettingssystem", $sActiveSettingsSystem);
		$oView->setData("homelink", $sHomeLink);
		$oView->setData("userslink", $sUsersLink);
		$oView->setData("settingsstandardlink", $sSettingsStandardLink);
		$oView->setData("settingssystemlink", $sSettingsSystemLink);
		$oView->setData("logoutlink", $sLogoutLink);
		
		return $oView->render();
>>>>>>> 140b415248d6a98d9f3aa37815964b3a4456cfd6
	}
}
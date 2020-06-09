<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://sabre.io/baikal
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

namespace BaikalAdmin\Controller\Navigation;

class Topbar extends \Flake\Core\Controller {
    function execute() {
    }

    function render() {
        $oView = new \BaikalAdmin\View\Navigation\Topbar();

        $sCurrentRoute = $GLOBALS["ROUTER"]::getCurrentRoute();
        $sActiveHome = $sActiveUsers = $sActiveSettingsStandard = $sActiveSettingsDatabase = "";

        $sControllerForDefaultRoute = $GLOBALS["ROUTER"]::getControllerForRoute("default");
        $sHomeLink = $sControllerForDefaultRoute::link();
        $sUsersLink = \BaikalAdmin\Controller\Users::link();
        $sSettingsStandardLink = \BaikalAdmin\Controller\Settings\Standard::link();
        $sSettingsDatabaseLink = \BaikalAdmin\Controller\Settings\Database::link();
        $sLogoutLink = \BaikalAdmin\Controller\Logout::link();

        if ($sCurrentRoute === "default") {
            $sActiveHome = "active";
        }
        if (
            $sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\Users") ||
            $sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\User\Calendars") ||
            $sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\User\AddressBooks")
        ) {
            $sActiveUsers = "active";
        }

        if ($sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\Settings\Standard")) {
            $sActiveSettingsStandard = "active";
        }

        if ($sCurrentRoute === $GLOBALS["ROUTER"]::getRouteForController("\BaikalAdmin\Controller\Settings\Database")) {
            $sActiveSettingsDatabase = "active";
        }

        $oView->setData("activehome", $sActiveHome);
        $oView->setData("activeusers", $sActiveUsers);
        $oView->setData("activesettingsstandard", $sActiveSettingsStandard);
        $oView->setData("activesettingsdatabase", $sActiveSettingsDatabase);
        $oView->setData("homelink", $sHomeLink);
        $oView->setData("userslink", $sUsersLink);
        $oView->setData("settingsstandardlink", $sSettingsStandardLink);
        $oView->setData("settingsdatabaselink", $sSettingsDatabaseLink);
        $oView->setData("logoutlink", $sLogoutLink);

        return $oView->render();
    }
}

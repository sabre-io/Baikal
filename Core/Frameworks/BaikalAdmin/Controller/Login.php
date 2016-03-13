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


namespace BaikalAdmin\Controller;

class Login extends \Flake\Core\Controller {

    function execute() {
    }

    function render() {
        $sActionUrl = \Flake\Util\Tools::getCurrentUrl();
        $sSubmittedFlagName = "auth";
        $sMessage = "";

        if (self::isSubmitted() && !\BaikalAdmin\Core\Auth::isAuthenticated()) {
            $sMessage = \Formal\Core\Message::error(
                "The login/password you provided is invalid. Please retry.",
                "Authentication error"
            );
        } elseif (self::justLoggedOut()) {
            $sMessage = \Formal\Core\Message::notice(
                "You have been disconnected from your session.",
                "Session ended",
                false
            );
        }

        $sLogin = htmlspecialchars(\Flake\Util\Tools::POST("login"));
        $sPassword = htmlspecialchars(\Flake\Util\Tools::POST("password"));

        if (trim($sLogin) === "") {
            $sLogin = "admin";
        }

        $oView = new \BaikalAdmin\View\Login();
        $oView->setData("message", $sMessage);
        $oView->setData("actionurl", $sActionUrl);
        $oView->setData("submittedflagname", $sSubmittedFlagName);
        $oView->setData("login", $sLogin);
        $oView->setData("password", $sPassword);

        return $oView->render();
    }

    protected static function isSubmitted() {
        return (intval(\Flake\Util\Tools::POST("auth")) === 1);
    }

    protected static function justLoggedOut() {
        $aParams = $GLOBALS["ROUTER"]::getURLParams();
        return (!empty($aParams) && $aParams[0] === "loggedout");
    }
}

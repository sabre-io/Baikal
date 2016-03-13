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


namespace BaikalAdmin\Core;

class Auth {
    static function isAuthenticated() {
        if (isset($_SESSION["baikaladminauth"]) && $_SESSION["baikaladminauth"] === md5(BAIKAL_ADMIN_PASSWORDHASH)) {
            return true;
        }

        return false;
    }

    static function authenticate() {

        if (intval(\Flake\Util\Tools::POST("auth")) !== 1) {
            return false;
        }

        $sUser = \Flake\Util\Tools::POST("login");
        $sPass = \Flake\Util\Tools::POST("password");

        $sPassHash = self::hashAdminPassword($sPass);

        if ($sUser === "admin" && $sPassHash === BAIKAL_ADMIN_PASSWORDHASH) {
            $_SESSION["baikaladminauth"] = md5(BAIKAL_ADMIN_PASSWORDHASH);
            return true;
        }

        return false;

    }

    static function unAuthenticate() {
        unset($_SESSION["baikaladminauth"]);
    }

    static function hashAdminPassword($sPassword) {
        if (defined("BAIKAL_AUTH_REALM")) {
            $sAuthRealm = BAIKAL_AUTH_REALM;
        } else {
            $sAuthRealm = "BaikalDAV";    # Fallback to default value; useful when initializing App, as all constants are not set yet
        }

        return md5('admin:' . $sAuthRealm . ':' . $sPassword);
    }

}

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

namespace BaikalAdmin\Core;

use Symfony\Component\Yaml\Yaml;

class Auth {
    static function isAuthenticated() {
        $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");

        if (isset($_SESSION["baikaladminauth"]) && $_SESSION["baikaladminauth"] === md5($config['system']['admin_passwordhash'])) {
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

        try {
            $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");
        } catch (\Exception $e) {
            error_log('Error reading baikal.yaml file : ' . $e->getMessage());

            return false;
        }
        $sPassHash = self::hashAdminPassword($sPass, $config['system']['auth_realm']);
        if ($sUser === "admin" && $sPassHash === $config['system']['admin_passwordhash']) {
            $_SESSION["baikaladminauth"] = md5($config['system']['admin_passwordhash']);

            return true;
        }

        return false;
    }

    static function unAuthenticate() {
        unset($_SESSION["baikaladminauth"]);
    }

    static function hashAdminPassword($sPassword, $sAuthRealm) {
        return hash('sha256', 'admin:' . $sAuthRealm . ':' . $sPassword);
    }
}

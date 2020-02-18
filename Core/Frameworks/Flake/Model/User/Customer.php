<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
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

namespace Flake\Model\User;

class Customer extends \Flake\Core\Model\Db implements \Flake\Model\IUser {
    const DATATABLE = "user";
    const PRIMARYKEY = "uid";
    const LABELFIELD = "username";

    protected $aData = [
        "username"  => "",
        "firstname" => "",
        "lastname"  => "",
        "email"     => "",
        "password"  => "",
        "salt"      => "",
        "crdate"    => 0,
        "enabled"   => 0,
    ];

    function isAdmin() {
        return false;
    }

    function getDisplayName() {
        return $this->get("firstname") . " " . $this->get("lastname");
    }

    function persist() {
    }

    function destroy() {
    }

    static function hashPassword($sClearPassword, $sSalt) {
        return sha1(APP_ENCRYPTION_KEY . ":" . $sClearPassword . ":" . $sSalt);
    }

    static function fetchByCredentials($sUsername, $sClearPassword) {
        # Algorithm:
        #	1- find the user by username
        #	2- hash the given password using the salt for this user
        #	3- compare hashes

        $oUser = self::getBaseRequester()
            ->addClauseEquals("username", $sUsername)
            ->addClauseEquals("enabled", 1)
            ->execute()
            ->first();

        if (is_null($oUser)) {
            return false;
        }

        if ($oUser->get("password") !== self::hashPassword($sClearPassword, $oUser->get("salt"))) {
            return false;
        }

        return $oUser;
    }
}

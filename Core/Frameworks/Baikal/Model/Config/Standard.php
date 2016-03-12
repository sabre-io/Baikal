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


namespace Baikal\Model\Config;

class Standard extends \Baikal\Model\Config {

    protected $aConstants = [
        "PROJECT_TIMEZONE" => [
            "type"    => "string",
            "comment" => "Timezone of the server; if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones",
        ],
        "BAIKAL_CARD_ENABLED" => [
            "type"    => "boolean",
            "comment" => "CardDAV ON/OFF switch; default TRUE",
        ],
        "BAIKAL_CAL_ENABLED" => [
            "type"    => "boolean",
            "comment" => "CalDAV ON/OFF switch; default TRUE",
        ],
        "BAIKAL_DAV_AUTH_TYPE" => [
            "type"    => "string",
            "comment" => "HTTP authentication type for WebDAV; default Digest"
        ],
        "BAIKAL_ADMIN_PASSWORDHASH" => [
            "type"    => "string",
            "comment" => "Baïkal Web admin password hash; Set via Baïkal Web Admin",
        ]
    ];

    # Default values
    protected $aData = [
        "PROJECT_TIMEZONE"          => "Europe/Paris",
        "BAIKAL_CARD_ENABLED"       => true,
        "BAIKAL_CAL_ENABLED"        => true,
        "BAIKAL_DAV_AUTH_TYPE"      => "Digest",
        "BAIKAL_ADMIN_PASSWORDHASH" => ""
    ];

    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        $oMorpho->add(new \Formal\Element\Listbox([
            "prop"       => "PROJECT_TIMEZONE",
            "label"      => "Server Time zone",
            "validation" => "required",
            "options"    => \Baikal\Core\Tools::timezones(),
        ]));


        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop"  => "BAIKAL_CAL_ENABLED",
            "label" => "Enable CalDAV"
        ]));

        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop"  => "BAIKAL_CARD_ENABLED",
            "label" => "Enable CardDAV"
        ]));

        $oMorpho->add(new \Formal\Element\Listbox([
            "prop"    => "BAIKAL_DAV_AUTH_TYPE",
            "label"   => "WebDAV authentication type",
            "options" => [ "Digest", "Basic" ]
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop"  => "BAIKAL_ADMIN_PASSWORDHASH",
            "label" => "Admin password",
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop"       => "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM",
            "label"      => "Admin password, confirmation",
            "validation" => "sameas:BAIKAL_ADMIN_PASSWORDHASH",
        ]));

        if (!defined("BAIKAL_ADMIN_PASSWORDHASH") || trim(BAIKAL_ADMIN_PASSWORDHASH) === "") {

            # No password set (Form is used in install tool), so password is required as it has to be defined
            $oMorpho->element("BAIKAL_ADMIN_PASSWORDHASH")->setOption("validation", "required");
        } else {
            $sNotice = "-- Leave empty to keep current password --";
            $oMorpho->element("BAIKAL_ADMIN_PASSWORDHASH")->setOption("placeholder", $sNotice);
            $oMorpho->element("BAIKAL_ADMIN_PASSWORDHASH_CONFIRM")->setOption("placeholder", $sNotice);
        }

        return $oMorpho;
    }

    function label() {
        return "Baïkal Settings";
    }

    function set($sProp, $sValue) {
        if ($sProp === "BAIKAL_ADMIN_PASSWORDHASH" || $sProp === "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM") {
            # Special handling for password and passwordconfirm

            if ($sProp === "BAIKAL_ADMIN_PASSWORDHASH" && $sValue !== "") {
                parent::set(
                    "BAIKAL_ADMIN_PASSWORDHASH",
                    \BaikalAdmin\Core\Auth::hashAdminPassword($sValue)
                );
            }

            return $this;
        }

        parent::set($sProp, $sValue);
    }

    function get($sProp) {
        if ($sProp === "BAIKAL_ADMIN_PASSWORDHASH" || $sProp === "BAIKAL_ADMIN_PASSWORDHASH_CONFIRM") {
            return "";
        }

        return parent::get($sProp);
    }

    protected function createDefaultConfigFilesIfNeeded() {

        # Create empty config.php if needed
        if (!file_exists(PROJECT_PATH_SPECIFIC . "config.php")) {
            @touch(PROJECT_PATH_SPECIFIC . "config.php");
            $sContent = "<?php\n" . \Baikal\Core\Tools::getCopyrightNotice() . "\n\n";
            $sContent .= $this->getDefaultConfig();
            file_put_contents(PROJECT_PATH_SPECIFIC . "config.php", $sContent);
        }

        # Create empty config.system.php if needed
        if (!file_exists(PROJECT_PATH_SPECIFIC . "config.system.php")) {
            @touch(PROJECT_PATH_SPECIFIC . "config.system.php");
            $sContent = "<?php\n" . \Baikal\Core\Tools::getCopyrightNotice() . "\n\n";
            $sContent .= $this->getDefaultSystemConfig();
            file_put_contents(PROJECT_PATH_SPECIFIC . "config.system.php", $sContent);
        }
    }

    protected static function getDefaultConfig() {

        $sCode = <<<CODE
##############################################################################
# Required configuration
# You *have* to review these settings for Baïkal to run properly
#

# Timezone of your users, if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
define("PROJECT_TIMEZONE", "Europe/Paris");

# CardDAV ON/OFF switch; default TRUE
define("BAIKAL_CARD_ENABLED", TRUE);

# CalDAV ON/OFF switch; default TRUE
define("BAIKAL_CAL_ENABLED", TRUE);

# WebDAV authentication type; default Digest
define("BAIKAL_DAV_AUTH_TYPE", "Digest");

# Baïkal Web admin password hash; Set via Baïkal Web Admin
define("BAIKAL_ADMIN_PASSWORDHASH", "");
CODE;
        $sCode = trim($sCode);
        return $sCode;
    }
}

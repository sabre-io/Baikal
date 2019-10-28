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


namespace Baikal\Model\Config;

use Symfony\Component\Yaml\Yaml;

class Standard extends \Baikal\Model\Config {

    protected $aConstants = [
        "project_timezone" => [
            "type"    => "string",
            "comment" => "Timezone of the server; if unsure, check http://en.wikipedia.org/wiki/List_of_tz_database_time_zones",
        ],
        "baikal_card_enabled" => [
            "type"    => "boolean",
            "comment" => "CardDAV ON/OFF switch; default TRUE",
        ],
        "baikal_cal_enabled" => [
            "type"    => "boolean",
            "comment" => "CalDAV ON/OFF switch; default TRUE",
        ],
        "baikal_invite_from" => [
            "type"    => "string",
            "comment" => "CalDAV invite From: mail address (comment or leave blank to disable notifications)",
        ],
        "baikal_dav_auth_type" => [
            "type"    => "string",
            "comment" => "HTTP authentication type for WebDAV; default Digest"
        ],
        "baikal_admin_passwordhash" => [
            "type"    => "string",
            "comment" => "Baïkal Web admin password hash; Set via Baïkal Web Admin",
        ]
    ];

    # Default values
    protected $aData = [
        "project_timezone"          => "Europe/Paris",
        "baikal_card_enabled"       => true,
        "baikal_cal_enabled"        => true,
        "baikal_invite_from"        => "",
        "baikal_dav_auth_type"      => "Digest",
        "baikal_admin_passwordhash" => "",
        "baikal_auth_realm"         => "BaikalDAV"
    ];

    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        $oMorpho->add(new \Formal\Element\Listbox([
            "prop"       => "project_timezone",
            "label"      => "Server Time zone",
            "validation" => "required",
            "options"    => \Baikal\Core\Tools::timezones(),
        ]));


        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop"  => "baikal_card_enabled",
            "label" => "Enable CardDAV"
        ]));

        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop"  => "baikal_cal_enabled",
            "label" => "Enable CalDAV"
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "baikal_invite_from",
            "label" => "Email invite sender address",
            "help"  => "Leave empty to disable sending invite emails"
        ]));

        $oMorpho->add(new \Formal\Element\Listbox([
            "prop"    => "baikal_dav_auth_type",
            "label"   => "WebDAV authentication type",
            "options" => ["Digest", "Basic"]
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop"  => "baikal_admin_passwordhash",
            "label" => "Admin password",
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop"       => "baikal_admin_passwordhash_confirm",
            "label"      => "Admin password, confirmation",
            "validation" => "sameas:baikal_admin_passwordhash",
        ]));

        try {
            $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "system.yaml");
        } catch (\Exception $e) {
            error_log('Error reading system.yaml file : ' . $e->getMessage());
        }

        if (!isset($config['parameters']["baikal_admin_passwordhash"]) || trim($config['parameters']["baikal_admin_passwordhash"]) === "") {

            # No password set (Form is used in install tool), so password is required as it has to be defined
            $oMorpho->element("baikal_admin_passwordhash")->setOption("validation", "required");
        } else {
            $sNotice = "-- Leave empty to keep current password --";
            $oMorpho->element("baikal_admin_passwordhash")->setOption("placeholder", $sNotice);
            $oMorpho->element("baikal_admin_passwordhash_confirm")->setOption("placeholder", $sNotice);
        }

        return $oMorpho;
    }

    function label() {
        return "Baïkal Settings";
    }

    function set($sProp, $sValue) {
        if ($sProp === "baikal_admin_passwordhash" || $sProp === "baikal_admin_passwordhash_confirm") {
            # Special handling for password and passwordconfirm

            if ($sProp === "baikal_admin_passwordhash" && $sValue !== "") {
                parent::set(
                    "baikal_admin_passwordhash",
                    \BaikalAdmin\Core\Auth::hashAdminPassword($sValue)
                );
            }

            return $this;
        }

        parent::set($sProp, $sValue);
    }

    function get($sProp) {
        if ($sProp === "baikal_admin_passwordhash" || $sProp === "baikal_admin_passwordhash_confirm") {
            return "";
        }

        return parent::get($sProp);
    }

    protected static function getDefaultConfig() {

        return [
            "project_timezone"          => "Europe/Paris",
            "baikal_card_enabled"       => true,
            "baikal_cal_enabled"        => true,
            "baikal_invite_from"        => "noreply@" . $_SERVER['SERVER_NAME'],
            "baikal_dav_auth_type"      => "Digest",
            "baikal_admin_passwordhash" => "",
            "baikal_auth_realm"         => "BaikalDAV",
        ];
    }
}

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
        "BAIKAL_DAV_LDAP_URI" => [
            "type" => "string",
            "comment" => "URI to LDAP Server (for ldap-userbind auth); default ldapi:///"
        ],
        "BAIKAL_DAV_LDAP_DN_TEMPLATE" => [
            "type" => "string",
            "comment" => "User DN for bind; with replacments %n => username, %u => user part, %d => domain part of username"
        ],
        "BAIKAL_DAV_LDAP_DISPLAYNAME_ATTR" => [
            "type" => "string",
            "comment" => "LDAP attribute for displayname; default cn"
        ],
        "BAIKAL_DAV_LDAP_EMAIL_ATTR" => [
            "type" => "string",
            "comment" => "LDAP attribute for email; default mail"
        ],
        "BAIKAL_DAV_MAIL_PROTOCOL" => [
            "type" => "string",
            "comment" => "used protocol; default 'imap (unencrypted)'"
        ],
        "BAIKAL_DAV_MAIL_SERVER" => [
            "type" => "string",
            "comment" => "host:port of mail server; default localhost:143"
        ],
        "BAIKAL_DAV_MAIL_CHECK_CERT" => [
            "type" => "boolean",
            "comment" => "validate ssl certificate; default yes"
        ],
        "BAIKAL_DAV_AUTO_CREATE_USER" => [
            "type" => "boolean",
            "comment" => "automatic creation of users; default yes"
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
        "BAIKAL_DAV_LDAP_URI" => "ldapi:///",
        "BAIKAL_DAV_LDAP_DN_TEMPLATE" => "uid=%n,dc=example,dc=com",
        "BAIKAL_DAV_LDAP_DISPLAYNAME_ATTR" => "cn",
        "BAIKAL_DAV_LDAP_EMAIL_ATTR" => "mail",
        "BAIKAL_DAV_MAIL_PROTOCOL" => "imap (unencrypted)",
        "BAIKAL_DAV_MAIL_SERVER" => "localhost:143",
        "BAIKAL_DAV_MAIL_CHECK_CERT" => true,
        "BAIKAL_DAV_AUTO_CREATE_USER" => true,
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
            "options" => [ "Digest", "Basic", "LDAP-UserBind", "Mail" ]
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "BAIKAL_DAV_LDAP_URI",
            "label" => "LDAP URI",
            "class" => "auth_ldap-userbind"
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "BAIKAL_DAV_LDAP_DN_TEMPLATE",
            "label" => "LDAP DN template",
            "class" => "auth_ldap-userbind",
            "popover" => [
                "title" => "posible placeholder",
                "content" => "<strong>%n</strong> - username<br /><strong>%u</strong> - user part of username , when it is an email address)<br /><strong>%d</strong> - domain part",
            ]
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "BAIKAL_DAV_LDAP_DISPLAYNAME_ATTR",
            "label" => "LDAP attribute for DisplayName",
            "class" => "auth_ldap-userbind"
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "BAIKAL_DAV_LDAP_EMAIL_ATTR",
            "label" => "LDAP attribute for eMail",
            "class" => "auth_ldap-userbind"
        ]));

        $oMorpho->add(new \Formal\Element\Listbox([
            "prop" => "BAIKAL_DAV_MAIL_PROTOCOL",
            "label" => "MailAuth Protocol",
            "class" => "auth_mail",
            "options" => [
                "imap" => "imap (unencrypted)",
                "imaps" => "imaps (SSL)",
                "imaptls" => "imap (StartTLS)",
                "pop3" => "pop3 (unencrypted)",
                "pop3s" => "pop3s (SSL)",
                "pop3tls" => "pop3 (StartTLS)",
                "smtp" => "smtp (unencrypted)",
                "smtps" => "smtps (SSL)",
                "smtptls" => "smtp (StartTLS)"
            ]
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "BAIKAL_DAV_MAIL_SERVER",
            "label" => "MailAuth Server",
            "class" => "auth_mail",
            "popover" => [
                "title" => "Format",
                "content" => "host:port"
            ]
        ]));

        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop" => "BAIKAL_DAV_MAIL_CHECK_CERT",
            "label" => "MailAuth Check SSL-Certificate",
            "class" => "auth_mail auth_mail_ssl",
            "popover" => [
                "title" => "Security",
                "content" => "validate the server certificate"
            ]
        ]));

        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop" => "BAIKAL_DAV_AUTO_CREATE_USER",
            "label" => "Automatically create users",
            "class" => "auth_mail auth_ldap-userbind"
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

# Auth Backend LDAP-UserBind; LDAP URI
define("BAIKAL_DAV_LDAP_URI", 'ldapi:///');

# Auth Backend LDAP-UserBind; Template for userbind
# %n => username
# %u => user part of username when it is an email
# %u => domain part of username when it is an email
define("BAIKAL_DAV_LDAP_DN_TEMPLATE", 'cn=%u,dc=%d,ou=domains,o=server');

# Auth Backend LDAP-UserBind; attribute for displayname
define("BAIKAL_DAV_LDAP_DISPLAYNAME_ATTR", 'cn');

# Auth Backend LDAP-UserBind; attribute for email
define("BAIKAL_DAV_LDAP_EMAIL_ATTR", 'mail');

# Auth Backend Mail; protocol of service
define("BAIKAL_DAV_MAIL_PROTOCOL", 'imap');

# Auth Backend Mail; server host:port
define("BAIKAL_DAV_MAIL_SERVER", 'localhost:143');

# Auth Backend Mail; validate the ssl-certificate
define("BAIKAL_DAV_MAIL_CHECK_CERT", TRUE);

# Auth Backends: automatic creation of users; default yes"
define("BAIKAL_DAV_AUTO_CREATE_USER", TRUE);

# Baïkal Web admin password hash; Set via Baïkal Web Admin
define("BAIKAL_ADMIN_PASSWORDHASH", "");
CODE;
        $sCode = trim($sCode);
        return $sCode;
    }
}

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


namespace Flake;

class Framework extends \Flake\Core\Framework {

    static function rmBeginSlash($sString) {
        if (substr($sString, 0, 1) === "/") {
            $sString = substr($sString, 1);
        }

        return $sString;
    }

    static function rmEndSlash($sString) {
        if (substr($sString, -1) === "/") {
            $sString = substr($sString, 0, -1);
        }

        return $sString;
    }

    static function appendSlash($sString) {
        if (substr($sString, -1) !== "/") {
            $sString .= "/";
        }

        return $sString;
    }

    static function prependSlash($sString) {
        if (substr($sString, 0, 1) !== "/") {
            $sString = "/" . $sString;
        }

        return $sString;
    }

    static function rmQuery($sString) {
        $iStart = strpos($sString, "?");
        return ($iStart === false) ? $sString : substr($sString, 0, $iStart);
    }

    static function rmScriptName($sString, $sScriptName) {
        $sScriptBaseName = basename($sScriptName);
        if (self::endswith($sString, $sScriptBaseName))
            return substr($sString, 0, -strlen($sScriptBaseName));
        return $sString;
    }

    static function rmProjectContext($sString) {
        return self::appendSlash(
            substr($sString, 0, -1 * strlen(PROJECT_CONTEXT_BASEURI))
        );
    }

    static function endsWith($sString, $sTest) {
        $iTestLen = strlen($sTest);
        if ($iTestLen > strlen($sString)) return false;
        return substr_compare($sString, $sTest, -$iTestLen) === 0;
    }

    static function bootstrap() {

        # Asserting PHP 5.5.0+
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            die('Flake Fatal Error: Flake requires PHP 5.5.0+ to run properly. Your version is: ' . PHP_VERSION . '.');
        }

        # Define safehash salt
        define("PROJECT_SAFEHASH_SALT", "strong-secret-salt");

        # Define absolute server path to Flake Framework
        define("FLAKE_PATH_ROOT", PROJECT_PATH_ROOT . "Core/Frameworks/Flake/");    # ./

        if (!defined('LF')) {
            define('LF', chr(10));
        }

        if (!defined('CR')) {
            define('CR', chr(13));
        }

        if (array_key_exists("SERVER_NAME", $_SERVER) && $_SERVER["SERVER_NAME"] === "mongoose") {
            define("MONGOOSE_SERVER", true);
        } else {
            define("MONGOOSE_SERVER", false);
        }

        # Undo magic_quotes as this cannot be disabled by .htaccess on PHP ran as CGI
        # Source: http://stackoverflow.com/questions/517008/how-to-turn-off-magic-quotes-on-shared-hosting
        # Also: https://github.com/netgusto/Baikal/issues/155
        if (in_array(strtolower(ini_get('magic_quotes_gpc')), ['1', 'on'])) {
            $process = [];
            if (isset($_GET) && is_array($_GET)) { $process[] = &$_GET;}
            if (isset($_POST) && is_array($_POST)) { $process[] = &$_POST;}
            if (isset($_COOKIE) && is_array($_COOKIE)) { $process[] = &$_COOKIE;}
            if (isset($_REQUEST) && is_array($_REQUEST)) { $process[] = &$_REQUEST;}

            while (list($key, $val) = each($process)) {
                foreach ($val as $k => $v) {
                    unset($process[$key][$k]);
                    if (is_array($v)) {
                        $process[$key][stripslashes($k)] = $v;
                        $process[] = &$process[$key][stripslashes($k)];
                    } else {
                        $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                }
            }

            unset($process);
        }

        # Fixing some CGI environments, that prefix HTTP_AUTHORIZATION (forwarded in .htaccess) with "REDIRECT_"
        if (array_key_exists("REDIRECT_HTTP_AUTHORIZATION", $_SERVER)) {
            $_SERVER["HTTP_AUTHORIZATION"] = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
        }

        #################################################################################################

        # determine Flake install root path
        # not using realpath here to avoid symlinks resolution

        define("PROJECT_PATH_CORE", PROJECT_PATH_ROOT . "Core/");
        define("PROJECT_PATH_CORERESOURCES", PROJECT_PATH_CORE . "Resources/");
        define("PROJECT_PATH_SPECIFIC", PROJECT_PATH_ROOT . "Specific/");
        define("PROJECT_PATH_FRAMEWORKS", PROJECT_PATH_CORE . "Frameworks/");
        define("PROJECT_PATH_WWWROOT", PROJECT_PATH_CORE . "WWWRoot/");

        require_once(PROJECT_PATH_CORE . "Distrib.php");

        define("PROJECT_PATH_DOCUMENTROOT", PROJECT_PATH_ROOT . "html/");

        # Determine PROJECT_BASEURI
        $sScript = substr($_SERVER["SCRIPT_FILENAME"], strlen($_SERVER["DOCUMENT_ROOT"]));
        $sDirName = str_replace("\\", "/", dirname($sScript));    # fix windows backslashes

        if ($sDirName !== ".") {
            $sDirName = self::appendSlash($sDirName);
        } else {
            $sDirName = "/";
        }

        $sBaseUrl = self::rmBeginSlash(self::rmProjectContext($sDirName));
        define("PROJECT_BASEURI", self::prependSlash($sBaseUrl));    # SabreDAV needs a "/" at the beginning of BASEURL

        # Determine PROJECT_URI
        $sProtocol = \Flake\Util\Tools::getCurrentProtocol();
        $sHttpBaseUrl = strtolower($_SERVER["REQUEST_URI"]);
        $sHttpBaseUrl = self::rmQuery($sHttpBaseUrl);
        $sHttpBaseUrl = self::rmScriptName($sHttpBaseUrl, $sScript);
        $sHttpBaseUrl = self::rmProjectContext($sHttpBaseUrl);
        define("PROJECT_URI", $sProtocol . "://" . $_SERVER["HTTP_HOST"] . $sHttpBaseUrl);
        unset($sScript); unset($sDirName); unset($sBaseUrl); unset($sProtocol); unset($sHttpBaseUrl);

        #################################################################################################

        # Include Flake Framework config
        require_once(FLAKE_PATH_ROOT . "config.php");

        # Determine Router class
        $GLOBALS["ROUTER"] = \Flake\Util\Tools::router();

        if (!\Flake\Util\Tools::isCliPhp()) {
            ini_set("html_errors", true);
            session_start();
            if (!isset($_SESSION['CSRF_TOKEN'])) {
                $_SESSION['CSRF_TOKEN'] = bin2hex(openssl_random_pseudo_bytes(20));
            }

        }

        setlocale(LC_ALL, FLAKE_LOCALE);
        date_default_timezone_set(FLAKE_TIMEZONE);

        $GLOBALS["TEMPLATESTACK"] = [];

        $aUrlInfo = parse_url(PROJECT_URI);
        define("FLAKE_DOMAIN", $_SERVER["HTTP_HOST"]);
        define("FLAKE_URIPATH", \Flake\Util\Tools::stripBeginSlash($aUrlInfo["path"]));
        unset($aUrlInfo);


        # Include Project config
        # NOTE: DB initialization and App config files inclusion
        # do not break execution if not properly executed, as
        # these errors will have to be caught later in the process
        # notably by the App install tool, if available; breaking right now
        # would forbid such install tool forwarding, for instance

        $sConfigPath = PROJECT_PATH_SPECIFIC . "config.php";
        $sConfigSystemPath = PROJECT_PATH_SPECIFIC . "config.system.php";

        if (file_exists($sConfigPath)) {
            require_once($sConfigPath);
        }

        if (file_exists($sConfigSystemPath)) {
            require_once($sConfigSystemPath);
        }

        self::initDb();
    }

    protected static function initDb() {
        # Dont init db on install, but in normal mode and when upgrading
        if (defined("BAIKAL_CONTEXT_INSTALL") && (!defined('BAIKAL_CONFIGURED_VERSION') || BAIKAL_CONFIGURED_VERSION === BAIKAL_VERSION)) {
            return true;
        }
        if (defined("PROJECT_DB_MYSQL") && PROJECT_DB_MYSQL === true) {
            self::initDbMysql();
        } else {
            self::initDbSqlite();
        }
    }

    protected static function initDbSqlite() {
        # Asserting DB filepath is set
        if (!defined("PROJECT_SQLITE_FILE")) {
            return false;
        }

        # Asserting DB file is writable
        if (file_exists(PROJECT_SQLITE_FILE) && !is_writable(PROJECT_SQLITE_FILE)) {
            die("<h3>DB file is not writable. Please give write permissions on file '<span style='font-family: monospace; background: yellow;'>" . PROJECT_SQLITE_FILE . "</span>'</h3>");
        }

        # Asserting DB directory is writable
        if (!is_writable(dirname(PROJECT_SQLITE_FILE))) {
            die("<h3>The <em>FOLDER</em> containing the DB file is not writable, and it has to.<br />Please give write permissions on folder '<span style='font-family: monospace; background: yellow;'>" . dirname(PROJECT_SQLITE_FILE) . "</span>'</h3>");
        }

        if (file_exists(PROJECT_SQLITE_FILE) && is_readable(PROJECT_SQLITE_FILE) && !isset($GLOBALS["DB"])) {
            $GLOBALS["DB"] = new \Flake\Core\Database\Sqlite(PROJECT_SQLITE_FILE);
            return true;
        }

        return false;
    }

    protected static function initDbMysql() {

        if (!defined("PROJECT_DB_MYSQL_HOST")) {
            die("<h3>The constant PROJECT_DB_MYSQL_HOST, containing the MySQL host name, is not set.<br />You should set it in Specific/config.system.php</h3>");
        }

        if (!defined("PROJECT_DB_MYSQL_DBNAME")) {
            die("<h3>The constant PROJECT_DB_MYSQL_DBNAME, containing the MySQL database name, is not set.<br />You should set it in Specific/config.system.php</h3>");
        }

        if (!defined("PROJECT_DB_MYSQL_USERNAME")) {
            die("<h3>The constant PROJECT_DB_MYSQL_USERNAME, containing the MySQL database username, is not set.<br />You should set it in Specific/config.system.php</h3>");
        }

        if (!defined("PROJECT_DB_MYSQL_PASSWORD")) {
            die("<h3>The constant PROJECT_DB_MYSQL_PASSWORD, containing the MySQL database password, is not set.<br />You should set it in Specific/config.system.php</h3>");
        }

        try {
            $GLOBALS["DB"] = new \Flake\Core\Database\Mysql(
                PROJECT_DB_MYSQL_HOST,
                PROJECT_DB_MYSQL_DBNAME,
                PROJECT_DB_MYSQL_USERNAME,
                PROJECT_DB_MYSQL_PASSWORD
            );

            # We now setup t6he connexion to use UTF8
            $GLOBALS["DB"]->query("SET NAMES UTF8");
        } catch (\Exception $e) {
            die("<h3>Baïkal was not able to establish a connexion to the configured MySQL database (as configured in Specific/config.system.php).</h3>");
        }

        return true;
    }

    static function isDBInitialized() {
        return isset($GLOBALS["DB"]) && \Flake\Util\Tools::is_a($GLOBALS["DB"], "\Flake\Core\Database");
    }
}

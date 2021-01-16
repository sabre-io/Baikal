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

namespace Baikal\Core;

class Tools {
    static function &db() {
        return $GLOBALS["pdo"];
    }

    static function assertEnvironmentIsOk() {
        # Asserting Baikal Context
        if (!defined("BAIKAL_CONTEXT") || BAIKAL_CONTEXT !== true) {
            exit("Bootstrap.php may not be included outside the Baikal context");
        }

        # Asserting PDO
        if (!defined('PDO::ATTR_DRIVER_NAME')) {
            exit('Baikal Fatal Error: PDO is unavailable. It\'s required by Baikal.');
        }

        # Asserting PDO::SQLite or PDO::MySQL
        $aPDODrivers = \PDO::getAvailableDrivers();
        if (!in_array('sqlite', $aPDODrivers, true) && !in_array('mysql', $aPDODrivers, true)) {
            exit('<strong>Baikal Fatal Error</strong>: Both <strong>PDO::sqlite</strong> and <strong>PDO::mysql</strong> are unavailable. One of them at least is required by Baikal.');
        }

        # Assert that the temp folder is writable
        if (!\is_writable(\sys_get_temp_dir())) {
            exit('<strong>Baikal Fatal Error</strong>: The system temp directory is not writable.');
        }
    }

    static function configureEnvironment() {
        set_exception_handler('\Baikal\Core\Tools::handleException');
        ini_set("error_reporting", E_ALL);
    }

    static function handleException($exception) {
        echo "<pre>" . $exception . "<pre>";
    }

    static function assertBaikalIsOk() {
        # DB connexion has not been asserted earlier by Flake, to give us a chance to trigger the install tool
        # We assert it right now
        if (!\Flake\Framework::isDBInitialized() && (!defined("BAIKAL_CONTEXT_INSTALL") || BAIKAL_CONTEXT_INSTALL === false)) {
            throw new \Exception("<strong>Fatal error</strong>: no connection to a database is available.");
        }

        # Asserting that the database is structurally complete
        #if(($aMissingTables = self::isDBStructurallyComplete($GLOBALS["DB"])) !== TRUE) {
        #	throw new \Exception("<strong>Fatal error</strong>: Database is not structurally complete; missing tables are: <strong>" . implode("</strong>, <strong>", $aMissingTables) . "</strong>");
        #}

        # Asserting config file exists
        if (!file_exists(PROJECT_PATH_CONFIG . "baikal.yaml")) {
            throw new \Exception("config/baikal.yaml does not exist. Please use the Install tool to create it or duplicate baikal.yaml.dist.");
        }

        # Asserting config file is readable
        if (!is_readable(PROJECT_PATH_CONFIG . "baikal.yaml")) {
            throw new \Exception("config/baikal.yaml is not readable. Please give read permissions to httpd user on file 'config/baikal.yaml'.");
        }

        # Asserting config file is writable
        if (!is_writable(PROJECT_PATH_CONFIG . "baikal.yaml")) {
            throw new \Exception("config/baikal.yaml is not writable. Please give write permissions to httpd user on file 'config/baikal.yaml'.");
        }
    }

    static function getRequiredTablesList() {
        return [
            "addressbooks",
            "calendarobjects",
            "calendars",
            "cards",
            "groupmembers",
            "locks",
            "principals",
            "users",
        ];
    }

    static function isDBStructurallyComplete(\Flake\Core\Database $oDB) {
        $aRequiredTables = self::getRequiredTablesList();
        $aPresentTables = $oDB->tables();

        $aIntersect = array_intersect($aRequiredTables, $aPresentTables);
        if (count($aIntersect) !== count($aRequiredTables)) {
            return array_diff($aRequiredTables, $aIntersect);
        }

        return true;
    }

    static function bashPrompt($prompt) {
        echo $prompt;
        @flush();
        @ob_flush();

        return @trim(fgets(STDIN));
    }

    static function bashPromptSilent($prompt = "Enter Password:") {
        $command = "/usr/bin/env bash -c 'echo OK'";

        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");

            return;
        }

        $command = "/usr/bin/env bash -c 'read -s -p \""
        . addslashes($prompt)
        . "\" mypassword && echo \$mypassword'";

        $password = rtrim(shell_exec($command));
        echo "\n";

        return $password;
    }

    static function getCopyrightNotice($sLinePrefixChar = "#", $sLineSuffixChar = "", $sOpening = false, $sClosing = false) {
        if ($sOpening === false) {
            $sOpening = str_repeat("#", 78);
        }

        if ($sClosing === false) {
            $sClosing = str_repeat("#", 78);
        }

        $iYear = date("Y");

        $sCode = <<<CODE
Copyright notice

(c) {$iYear} Jérôme Schneider <mail@jeromeschneider.fr>
All rights reserved

http://sabre.io/baikal

This script is part of the Baïkal Server project. The Baïkal
Server project is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

The GNU General Public License can be found at
http://www.gnu.org/copyleft/gpl.html.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

This copyright notice MUST APPEAR in all copies of the script!
CODE;
        $sCode = "\n" . trim($sCode) . "\n";
        $aCode = explode("\n", $sCode);
        foreach (array_keys($aCode) as $iLineNum) {
            $aCode[$iLineNum] = trim($sLinePrefixChar . "\t" . $aCode[$iLineNum]);
        }

        if (trim($sOpening) !== "") {
            array_unshift($aCode, $sOpening);
        }

        if (trim($sClosing) !== "") {
            $aCode[] = $sClosing;
        }

        return implode("\n", $aCode);
    }

    static function timezones() {
        $aZones = \DateTimeZone::listIdentifiers();

        reset($aZones);

        return $aZones;
    }
}

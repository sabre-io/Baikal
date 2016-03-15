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

class Database extends \Baikal\Model\Config {

    protected $aConstants = [
        "PROJECT_SQLITE_FILE" => [
            "type"    => "litteral",
            "comment" => "Define path to Baïkal Database SQLite file",
        ],
        "PROJECT_DB_MYSQL" => [
            "type"    => "boolean",
            "comment" => "MySQL > Use MySQL instead of SQLite ?",
        ],
        "PROJECT_DB_MYSQL_HOST" => [
            "type"    => "string",
            "comment" => "MySQL > Host, including ':portnumber' if port is not the default one (3306)",
        ],
        "PROJECT_DB_MYSQL_DBNAME" => [
            "type"    => "string",
            "comment" => "MySQL > Database name",
        ],
        "PROJECT_DB_MYSQL_USERNAME" => [
            "type"    => "string",
            "comment" => "MySQL > Username",
        ],
        "PROJECT_DB_MYSQL_PASSWORD" => [
            "type"    => "string",
            "comment" => "MySQL > Password",
        ],
    ];

    # Default values
    protected $aData = [
        "PROJECT_SQLITE_FILE"       => 'PROJECT_PATH_SPECIFIC . "db/db.sqlite"',
        "PROJECT_DB_MYSQL"          => false,
        "PROJECT_DB_MYSQL_HOST"     => "",
        "PROJECT_DB_MYSQL_DBNAME"   => "",
        "PROJECT_DB_MYSQL_USERNAME" => "",
        "PROJECT_DB_MYSQL_PASSWORD" => "",
    ];

    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "PROJECT_SQLITE_FILE",
            "label"      => "SQLite file path",
            "validation" => "required",
            "inputclass" => "input-xxlarge",
            "help"       => "The absolute server path to the SQLite file",
        ]));

        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop"            => "PROJECT_DB_MYSQL",
            "label"           => "Use MySQL",
            "help"            => "If checked, Baïkal will use MySQL instead of SQLite.",
            "refreshonchange" => true,
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "PROJECT_DB_MYSQL_HOST",
            "label" => "MySQL host",
            "help"  => "Host ip or name, including <strong>':portnumber'</strong> if port is not the default one (3306)"
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "PROJECT_DB_MYSQL_DBNAME",
            "label" => "MySQL database name",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "PROJECT_DB_MYSQL_USERNAME",
            "label" => "MySQL username",
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop"  => "PROJECT_DB_MYSQL_PASSWORD",
            "label" => "MySQL password",
        ]));

        return $oMorpho;
    }

    function label() {
        return "Baïkal Database Settings";
    }

    protected static function getDefaultConfig() {
        throw new \Exception("Should never reach getDefaultConfig() on \Baikal\Model\Config\Database");
    }
}

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

class Database extends \Baikal\Model\Config {

    protected $aConstants = [
        "project_sqlite_file" => [
            "type"    => "litteral",
            "comment" => "Define path to Baïkal Database SQLite file",
        ],
        "project_db_mysql" => [
            "type"    => "boolean",
            "comment" => "MySQL > Use MySQL instead of SQLite ?",
        ],
        "project_db_mysql_host" => [
            "type"    => "string",
            "comment" => "MySQL > Host, including ':portnumber' if port is not the default one (3306)",
        ],
        "project_db_mysql_dbname" => [
            "type"    => "string",
            "comment" => "MySQL > Database name",
        ],
        "project_db_mysql_username" => [
            "type"    => "string",
            "comment" => "MySQL > Username",
        ],
        "project_db_mysql_password" => [
            "type"    => "string",
            "comment" => "MySQL > Password",
        ],
    ];

    # Default values
    protected $aData = [
        "project_sqlite_file"       => PROJECT_PATH_SPECIFIC . "db/db.sqlite",
        "project_db_mysql"          => false,
        "project_db_mysql_host"     => "",
        "project_db_mysql_dbname"   => "",
        "project_db_mysql_username" => "",
        "project_db_mysql_password" => "",
    ];

    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "project_sqlite_file",
            "label"      => "SQLite file path",
            "validation" => "required",
            "inputclass" => "input-xxlarge",
            "help"       => "The absolute server path to the SQLite file",
        ]));

        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop"            => "project_db_mysql",
            "label"           => "Use MySQL",
            "help"            => "If checked, Baïkal will use MySQL instead of SQLite.",
            "refreshonchange" => true,
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "project_db_mysql_host",
            "label" => "MySQL host",
            "help"  => "Host ip or name, including <strong>':portnumber'</strong> if port is not the default one (3306)"
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "project_db_mysql_dbname",
            "label" => "MySQL database name",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "project_db_mysql_username",
            "label" => "MySQL username",
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop"  => "project_db_mysql_password",
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

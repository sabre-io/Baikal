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
    # Default values
    protected $aData = [
        "sqlite_file"    => PROJECT_PATH_SPECIFIC . "db/db.sqlite",
        "backend"        => "",
        "mysql_host"     => "",
        "mysql_dbname"   => "",
        "mysql_username" => "",
        "mysql_password" => "",
        "mysql_ca_cert"  => "",
        "encryption_key" => "",
        "pgsql_host"     => "",
        "pgsql_dbname"   => "",
        "pgsql_username" => "",
        "pgsql_password" => "",
    ];

    function __construct() {
        parent::__construct("database");
    }

    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        $oMorpho->add(new \Formal\Element\Listbox([
            "prop"       => "backend",
            "label"      => "Database Backend",
            "validation" => "required",
            "options"    => ['sqlite', 'mysql', 'pgsql'],
            "refreshonchange" => true,
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "sqlite_file",
            "label"      => "SQLite file path",
            "validation" => "required",
            "inputclass" => "input-xxlarge",
            "help"       => "The absolute server path to the SQLite file",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "mysql_host",
            "label" => "MySQL host",
            "help"  => "Host ip or name, including ':portnumber' if port is not the default one (3306)",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "mysql_dbname",
            "label" => "MySQL database name",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "mysql_username",
            "label" => "MySQL username",
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop"  => "mysql_password",
            "label" => "MySQL password",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "mysql_ca_cert",
            "label" => "MySQL CA Certificate",
            "help"  => "Optional. Leave blank to ignore",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "pgsql_host",
            "label" => "PostgreSQL host",
            "help" => "Host ip or name, including ':portnumber' if port is not the default one",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "pgsql_dbname",
            "label" => "PostgreSQL database name",
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop" => "pgsql_username",
            "label" => "PostgreSQL username",
        ]));

        $oMorpho->add(new \Formal\Element\Password([
            "prop" => "pgsql_password",
            "label" => "PostgreSQL password",
        ]));

        return $oMorpho;
    }

    function label() {
        return "Baïkal Database Settings";
    }
}

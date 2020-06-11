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
        "mysql"          => false,
        "mysql_host"     => "",
        "mysql_dbname"   => "",
        "mysql_username" => "",
        "mysql_password" => "",
        "encryption_key" => "",
    ];

    function __construct() {
        parent::__construct("database");
    }

    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "sqlite_file",
            "label"      => "SQLite file path",
            "validation" => "required",
            "inputclass" => "input-xxlarge",
            "help"       => "The absolute server path to the SQLite file",
        ]));

        $oMorpho->add(new \Formal\Element\Checkbox([
            "prop"            => "mysql",
            "label"           => "Use MySQL",
            "help"            => "If checked, Baïkal will use MySQL instead of SQLite.",
            "refreshonchange" => true,
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "mysql_host",
            "label" => "MySQL host",
            "help"  => "Host ip or name, including ':portnumber' if port is not the default one (3306)"
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

        return $oMorpho;
    }

    function label() {
        return "Baïkal Database Settings";
    }
}

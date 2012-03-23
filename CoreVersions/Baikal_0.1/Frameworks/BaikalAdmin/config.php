<?php

define("BAIKALADMIN_PATH_ROOT", BAIKAL_PATH_FRAMEWORKS . "BaikalAdmin/");
define("BAIKALADMIN_PATH_TEMPLATES", BAIKALADMIN_PATH_ROOT . "Resources/Templates/");

define("FLAKE_DB_FILEPATH", BAIKAL_SQLITE_FILE);
define("FLAKE_TIMEZONE", BAIKAL_TIMEZONE);
define("FLAKE_PATH_FRAMEWORKS", BAIKAL_PATH_FRAMEWORKS);
define("FLAKE_PATH_ROOT", BAIKAL_PATH_FRAMEWORKS . "Flake/");
define("FLAKE_PATH_WWWROOT", BAIKAL_PATH_WWWROOT);
define("FLAKE_SAFEHASH_SALT", "une-clef-super-secrete");
define("FLAKE_LOCALE", "fr_FR.UTF-8");

# TODO: CHANGE THIS
define("FLAKE_BASEURL", "http://baikal.jeromeschneider.fr/");

$GLOBALS["ROUTES"] = array(
	"default" => "\BaikalAdmin\Route\User\Listing",
);
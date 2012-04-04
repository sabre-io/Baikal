<?php

define("BAIKALADMIN_PATH_TEMPLATES", BAIKALADMIN_PATH_ROOT . "Resources/Templates/");

$GLOBALS["ROUTES"] = array(
	"default" => "\BaikalAdmin\Route\Dashboard",
	"users" => "\BaikalAdmin\Route\Users",
	"users/details" => "\BaikalAdmin\Route\Details",
	"install" => "\BaikalAdmin\Route\Install"
);
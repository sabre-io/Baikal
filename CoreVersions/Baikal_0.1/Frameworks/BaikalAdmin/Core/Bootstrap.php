<?php

define("BAIKALADMIN_PATH_ROOT", dirname(dirname(__FILE__)) . "/");

# Bootstrap Baikal Core
require_once(dirname(dirname(dirname(__FILE__))) . "/Baikal/Core/Bootstrap.php");	# ../../, symlink-safe

# Bootstrap Flake
require_once(dirname(dirname(dirname(__FILE__))) . "/Flake/Core/Bootstrap.php");

# Registering BaikalAdmin classloader
require_once(dirname(__FILE__) . '/ClassLoader.php');
\BaikalAdmin\Core\ClassLoader::register();

# Include BaikalAdmin Framework config
require_once(BAIKALADMIN_PATH_ROOT . "config.php");
<?php

define("BAIKALADMIN_PATH_ROOT", dirname(dirname(__FILE__)) . "/");

# Bootstrap Baïkal Core
require_once(dirname(dirname(dirname(__FILE__))) . "/Baikal/Core/Bootstrap.php");	# ../../, symlink-safe

# Bootstrap Flake
require_once(dirname(dirname(dirname(__FILE__))) . "/Flake/Core/Bootstrap.php");

# Bootstrap Formal
require_once(dirname(dirname(dirname(__FILE__))) . "/Formal/Core/Bootstrap.php");

# Registering BaikalAdmin classloader
require_once(dirname(__FILE__) . '/ClassLoader.php');
\BaikalAdmin\Core\ClassLoader::register();

# Relative to BAIKAL_URI; so that BAIKAL_URI . BAIKALADMIN_URIPATH corresponds to the full URL to Baïkal admin
define("BAIKALADMIN_URIPATH", "admin/");

# Include BaikalAdmin Framework config
require_once(BAIKALADMIN_PATH_ROOT . "config.php");
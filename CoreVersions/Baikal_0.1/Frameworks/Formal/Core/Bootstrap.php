<?php

define("FORMAL_PATH_ROOT", dirname(dirname(__FILE__)) . "/");

# Registering BaikalAdmin classloader
require_once(dirname(__FILE__) . '/ClassLoader.php');
\Formal\Core\ClassLoader::register();
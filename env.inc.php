<?php

# Define Named constants
function addDefine($name, $value) {
	if(!defined($name)) {
		define($name, $value);
	}
}

function debug_msg($msg) {
	$date_now = date("F j, Y, g:i a");
	$msg = "[ $date ] $msg";
	error_log($msg, 3, DEBUG_LOGS);
}

# Load Autoloader
require_once(dirname(__FILE__) . "/Autoloader.class.php");

# Autoloader Add paths
Autoloader::addDirClasses(dirname(__FILE__) . "/models");
Autoloader::addDirClasses(dirname(__FILE__) . "/lib");


# Directories
define('CONF_DIR', dirname(__FILE__) . "/config");
define('LIB_DIR',  dirname(__FILE__) . "/lib");

# Mysql
define("MYSQL_CONF_FILE", CONF_DIR . "/mysql.config.ini");

# Logging
define('DEBUG_LOGS', '/var/log/genapi/genapi.log');

# Timezone
date_default_timezone_set('America/Los_Angeles');





<?php
require_once dirname(__FILE__) . '/../env.inc.php';
addDefine('DB_SCHEMA_DIR', dirname(__FILE__) . "/db_schema");

$schema_files = scandir(DB_SCHEMA_DIR);
foreach ($schema_files as $f) {
	if(preg_match("/table/", $f)) {
		require_once(DB_SCHEMA_DIR . "/" .$f);
		$table_name = preg_replace("/.table.php/", "", $f);
		$class_name = ucfirst($table_name) . "Table";
		call_user_func_array(array($class_name, 'create'), array());
	}
}
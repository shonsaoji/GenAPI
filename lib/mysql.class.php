<?php
# define("CONF_DIR", dirname(__FILE__) . "/../config");
# define("MYSQL_CONF_FILE", CONF_DIR . "/mysql.config.ini");
class Mysql {
	
	
	public static function connect($connection_params = null) {
		if(!$connection_params) {
			$connection_params = parse_ini_file(MYSQL_CONF_FILE);
		}
		$link = mysql_connect($connection_params['host'], $connection_params['dbuser'], $connection_params['dbpassword']);

		if($link) {
			error_log("\n[OK] Established connection with mysql\n");
		} else {
			error_log("\n[ERROR] Could not establish connection\n");
		}

		if (!mysql_select_db($connection_params['dbname'], $link)) {
			error_log('[ERROR] Could not select database');
			return false;
		}

		return $link;
	}


	public static function execute($query, $link = null) {
		$date_now = date("F j, Y, g:i a");
		
		if(!$link) {
			self::connect();
		}

		$result = mysql_query($query);
		
		if(!$result) {
			error_log("[ $date_now ]  [ERROR] : Could not execute query : $query\n");
			echo mysql_error() . "\n\n";
		} else {
			error_log( "[ $date_now ] [OK] : Executed query : $query \n");
		}
		return $result;

	}
}

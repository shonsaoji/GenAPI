<?php
class UsersTable {
	public static function create() {
		$query = "CREATE TABLE IF NOT EXISTS users (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					INDEX(id), 
					created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
					first_name VARCHAR (100), 
					last_name VARCHAR(100), 
					email VARCHAR(50), 
					sex INTEGER, 
					password VARCHAR(100), 
					salt VARCHAR(100), 
					location VARCHAR(100), 
					birthday datetime,
					fbid VARCHAR(50), INDEX (fbid)
				  )";
	
		Mysql::execute($query);
	}

	public static function destroy() {
		$query = "DROP TABLE users";
		Mysql::execute($query);
	}
}


<?php

class Autoloader {
	public static function addDirClasses($dir_path) {
		if(!is_dir($dir_path)) {
			error_log(__METHOD__ . " : Not a directory $dir_path");
			return false;
		}
		
		# Get All files
		$dir_contents = scandir($dir_path);
		foreach($dir_contents as $f) {
			if(preg_match("/class.php/", $f)) {
				# Require All Files with class in the name
				require_once($dir_path . "/" .$f);
			}
		}
	}
}
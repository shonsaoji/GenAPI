<?php

class BaseModel {
  /*
	 * Handler for post requests
	 */
	public static function process_post_request($model, $action, $params, $format = 'json') {
	
/*
		if(User::get_logged_in_user_id() === false) {
			$_SESSION['error_flash'] = "User not logged in";
			error_log(__METHOD__ . " User not logged in");
			return false;
		}
*/
		if(get_magic_quotes_gpc()){
			$params = stripslashes($params);
		}

	
		$format = strtolower($format);
	

		$blob = null;
		switch($format) {
			case 'json':
				$blob = json_decode($params, true);
				break;
			case 'php-serialized':
				$blob = unserialize($params);
				break;
			default:
				break;
		}
	
		if($blob == null) {
			error_log(__METHOD__ . " Blob is null. Params = $params");
			error_log(__METHOD__ . " Blob is null. Blob = $blob");
			return;
		}
		
		$class= ucfirst($model);
		$mgr = new $class();
		$out = call_user_func_array(array($mgr, $action), array($blob));
		if(!$out) {
			error_log(__METHOD__ . " Something went wrong. $action returned false");
		}
		return $out;
	}
}

/*
 * TEST
$bm = BaseModel::process_post_request("whine", "save", array());
*/

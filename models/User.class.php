<?php
class User {
	const MALE = 1;
	const FEMALE = 2;
	
	public static function process_post_request($action, $blob) {
		$user_mgr = new User();
		$out = call_user_func_array(array($user_mgr, $action), array($blob));
		if(!$out) {
			echo 'Error \n';
		}
	}
	
	/*
	 * Saves User Blob to Storage
	 * Pass $blob which is array having following fields
	 * first_name
	 * last_name
	 * email
	 * password
	 * sex
	 * fbid
	 */
	public function save($blob) {
		if(!$blob)
			return false;

		if(!isset($blob['first_name']) || !isset($blob['last_name']) || !isset($blob['email']) || !isset($blob['password']) || !isset($blob['sex'])) {
			$_SESSION['error_flash'] = "Please enter valid information to register";
			return false;
		}
		
		$first_name = prune($blob['first_name']);
		$last_name  = prune($blob['last_name']);
		$email 		= prune($blob['email']);
		$password   = sha1(prune($blob['password']));
		$sex = $blob['sex'];
		$location = isset($blob['location']) ? prune($blob['location']) : null;
		$birthday = isset($blob['birthday']) ? prune($blob['birthday']) : null;
		
		if(!isset($blob['fbid']))
			$fbid = "";
		else
			$fbid = prune($blob['fbid']);
				
		$user_id_query = "SELECT id, first_name, last_name FROM users WHERE email='".$email."'";
		$user_result = Mysql::execute($user_id_query);
		$user = mysql_fetch_assoc($user_result);
		if(!$user)
		{
			# Save only if user doesnt exist
			$user_query = "INSERT INTO users(first_name, last_name, email, password, sex, location, birthday, fbid) VALUES ('".$first_name."','".$last_name."', '".$email."', '".$password."', '".$sex."', '".$location."','".$birthday."', '$fbid')";
	
			$saved = Mysql::execute($user_query);
			if(!$saved) {
				error_log(__METHOD__ . " Unable to save user");
				return false;
			}
	
			$user_id_query = "SELECT id, first_name, last_name FROM users where email='".$email."'";
			$user_result = Mysql::execute($user_id_query);
			$user = mysql_fetch_assoc($user_result);
			if(!$user) {
				error_log(__METHOD__ . " Unable to fetch user summary from DB for User ( Email address = $email )");
				return false;
			}
		}
	
		$_SESSION['user'] = array('id' => $user['id'], 'first' => $user['first_name'], 'last' => $user['last']);
		return true;
	}
	
	/*
	 * Login User
	 * Check if user exists?
	 * Get User ID
	 * Set Session	

	 * Pass $blob which is array with following fields
	 * login_email
	 * login_pwd
	 */
	public function login($blob) {
		$email = prune($blob['login_email']);
		$pwd = prune($blob['login_pwd']);
		$password = sha1($pwd);
		
		$proceed = false;
		$user_id_query = "SELECT id, password, first_name, last_name FROM users where email='".$email."'";
		$user_result = Mysql::execute($user_id_query);
		$user = mysql_fetch_assoc($user_result);
		if(!$user)
		{  // hasnt registered
			$_SESSION['error_flash'] = "User name found. Have you registered?";
			error_log("\nCould not find user\n");
			$proceed = false;
		} else {
			# Check password
			if($user['password'] === $password) {
				$proceed = true;
			} else {
				# Wrong Password
				$_SESSION['error_flash'] = "Could not authenticate. Please check your password";
				error_log("\nCould not authenticate user\n");
				$proceed = false;
			}
		}
		
		if($proceed === true) {
			$_SESSION['user'] = array('id' => $user['id'], 'first' => $user['first_name'], 'last' => $user['last_name']);
			error_log("User ID in session = ".$user['id']."\n");
		}
		return $proceed;
	}
	
	public function logout() {
		# Clear Session
		unset($_SESSION['user']); 
	}
	
	# Get Request to check if FB User exists
	public function login_fb_user($fbid) {
		$query = "SELECT id, first_name, last_name FROM users WHERE fbid='$fbid'";
		$result = Mysql::execute($query);
		
		if(!$result) {
			# Query could not be executed
			return false;
		}
		$user = mysql_fetch_assoc($result);
		
		if(!$user) {
			$_SESSION['error_flash'] = "IMW account for FB User (fbid = $fbid)  does not exist. Please register";
			error_log(__METHOD__ . "IMW account for FB User (fbid = $fbid)  does not exist. Please register");
			return false;
		}
		
		$_SESSION['user'] = array('id' =>  $user['id'], 'first' => $user['first_name'], 'last' => $user['last_name']);
		return true;
	}
	
	public static function get_logged_in_user_id() {
		$user_summary = $_SESSION['user'];
		return $user_summary['id'];
	}

	public function get_logged_in_user_full_name() {
		$user_summary = $_SESSION['user']; 
		return $user_summary['first'] . ' ' . $user_summary['last'];
	}
	
}
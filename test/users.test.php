<?php
require_once dirname(__FILE__) . '/../env.inc.php';

$function = $argv[1];
$param = $argv[2];
switch($function) {
	case 'create':
	case 'login' :
	case 'fb_login' :
	case 'get_logged_in_user':
		$out = call_user_func($function, $param);
		break;
	default:
		echo "Not Supported\n";
		exit;
}
if($out) {
	$user = $_SESSION['user'];
	echo json_encode($user);
	echo "\n\n";
	
}


function create() {
	$j_user = array(
			'first_name' => 'XYZ',
			'last_name' => 'PQR',
			'email' => 'ss@whatever.com',
			'sex' => User:MALE,
			'password' => 'dummyPass',
			'location' => 'Somewhere',
			'birthday' => 'Somedate',
			'fbid' => '123897132'
			);
	$result = User::process_post_request('save', $j_user);
	if(!$result) {
		echo 'Error\n\n';
	}
}

function login() {
	$blob = array('login_email' => 'ss@whatever.com', 'login_pwd' => 'dummyPass');
	$result = User::process_post_request('login', $blob);
	return $result;
}

function logout() {
	
}

function fb_login($fbid) {
	$fbid = '123897132';
	$status = User::process_post_request('login_fb_user', $fbid);
}

function get_logged_in_user() {
	echo User::get_logged_in_user_id();
}




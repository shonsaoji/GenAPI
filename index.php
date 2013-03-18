<?php
# Author : shon.saoji@gmail.com
# Date   : Aug 25, 12
# Time   : 10:55 am 

# API to post & get data
# GET
# URI FORMAT : /model/action/id

# POST


# Get Env File
require_once dirname(__FILE__) . '/env.inc.php';

# ---
# Get All Server Parameters
# ---

$path   = $_GET['path'];

$method = $_SERVER['REQUEST_METHOD'];

$req = new GenAPI($path, $method);
$req->handle();


class GenAPI {
	
	private $path;
	private $method;
	private $model;
	private $action;
	private $id;
	
	private $error_msg;
	private $status;

	# Response objects
	private $response_msg;
	private $response_code;
	private $response_format;
	
	public function __construct($path, $method) {
		$this->path   = $path;
		$this->method = $method;
	}

	/*
	 * Handle Request
	 */
	public function handle() {
		$this->get_uri_parameters();
		$this->process_request();
		$this->send_response();
	}
	
	/*
	 *  Parse the URL & Get parameters
	*/
	private function get_uri_parameters() {
		$url_params = explode("/", $this->path);
		$count = count($url_params);
		if($count < 3) {
			$this->error_msg = "Invalid Number of arguments";
			$this->status = -1;
		}
	
		if(isset($url_params[1])) {
			$this->model = trim($url_params[1]);
		}
	
		if(isset($url_params[2])) {
			$this->action = trim($url_params[2]);
		}
	
		if(isset($url_params[3])) {
			$this->id = trim($url_params[3]);
		}
	}
	
	/*
	 * Sends the HTTP Response based on 
	 */
	private function send_response() {
		$response = array();
		$response['message'] = $this->response_msg;
		$response['code'] = $this->response_code;
		$response['format'] = $this->response_format;
		die(json_encode($response));
	}
	
	/*
	 * Request Handler
	 */
	private function process_request() {
		$obj = false;
		if(strcasecmp($this->method, "post") == 0) {
			# Post Request
			$obj = $this->process_post_request();
		} else {
			# Get Request
			$obj = $this->process_get_request();
		}

		# Generate Response
		if($obj === false || $obj === null) {
			$this->response_code = 500;
			$this->response_msg = isset($_SESSION['error_flash']) ? $_SESSION['error_flash'] : "Something went wrong";
			$_SESSION['error_flash'] = "";
		} else {
			$this->response_msg = $obj;
			$this->response_code = 200;
			$this->response_format = 'json';
		}
	}
	
	/*
	 * Processes the get request 
	 * Redirects the request to execute the Model / Action
	 */
	private function process_get_request() {
		$obj = null;
		$class_name = ucfirst($this->model);
		
		try {
			$obj = call_user_func_array(array(new $class_name, $this->action), array($this->id));
		} catch (Exception $e) {
			error_log(__METHOD__ . "Caught Exception\n");
		}
		return $obj;
	}
	
	private function process_post_request() {
		
		$blob = null;
		if(!isset($_POST['blob'])) {
			$this->response_code = 500;
			$this->response_msg = "'blob' post parameter must be set. check readme for the api";
			error_log(__METHOD__ . " 'blob' post parameter must be set. check readme for the api");
			return;
		} else {
			$blob = $_POST['blob'];
		}
		
		if(!isset($_POST['format'])) {
			$format = 'json';
		} else {
			$format = $_POST['format'];
		}
		
		$class_name = ucfirst($this->model);
		$obj = call_user_func_array(array($class_name, 'process_post_request'), array($this->action, $blob, $format));
		return $obj;
	}
	
}

?>

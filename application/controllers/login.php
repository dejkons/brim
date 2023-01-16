<?php
class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model("user_model");
		$this->load->library('session');
	}

	public function index() {
		/*primary data*/
		// check if user is already logged in
		$error = "";

		if ($currentUser = $this->user_model->getCurrentUser()) {
			header("Location: " . $this->config->item('base_url'));
			die();
		}

		// login
		if ($this->input->post("login") == 1) {

			$username = trim($this->input->post("username", true));
			$password = trim($this->input->post("password", true));

			if (($username != '') && ($password != '')) {
				if (!$user = $this->user_model->checkCredentials($username, $password)) {
					$error = "Parameters are not valid";
				} else {
					$this->user_model->login($user->userID);
					header("Location: " . $this->config->item('base_url'));
					die();
				}
			}

		}

		// basic data
		$data['css']   = $this->config->item('css');
		$data['title'] = 'BRIM Administration' . " - " . "Login";
		$data['base']  = $this->config->item('base_url');
		$data['errorMessage'] = $error;


		/*shooting page*/
		$this->load->view("login_view", $data);
	}
}


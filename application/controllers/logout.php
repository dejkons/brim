<?php 

class Logout extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->library("session");
		$this->load->model("user_model");
		if (!$this->user_model->getCurrentUser()) {
			header("Location: " . $this->config->item("base_url") . "/logout");
		}
	}
	
	public function index() {
        $this->session->sess_destroy();
	    header("Location: " . $this->config->item('base_url') . '/login');
        die();
	}
}

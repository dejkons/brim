<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller {
    public $title = "BRIM";
    public $system;

    function __construct() {
        parent::__construct();
        define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

}

// END MY_Controller Class



/* End of file MY_Controller.php */

/* Location: ./system/libraries/MY_Controller.php */

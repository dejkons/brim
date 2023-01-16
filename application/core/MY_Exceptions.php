<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions{
    
    public function __construct() {
        parent::__construct();
    }

    function show_404($page = '', $log_error = TRUE) {
        $heading = "404 Page Not Found";
        $message = "The page you requested was not found.";
        
        if (!function_exists('base_url')) {
            global $CFG;
            $base_url = $CFG->config['base_url'];
        } else {
            $base_url = substr(base_url(), 0, strlen(base_url()) - 1);
        }

        // By default we log this, but allow a dev to skip it
        if ($log_error) {
            log_message('error', '404 Page Not Found --> '.$page);
        }

        include(APPPATH.'errors/error_404'.EXT);
        exit;
    }
}
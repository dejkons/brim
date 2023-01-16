<?php

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
		$this->load->database();
		$this->load->library('session');
    }

    function login($userID) {
        // check if userID exists
        if (!$user = $this->getUserByID($userID)) return false;

        // check if user is active
        if ($user->active == 'true') {
			// set session and, if successfull, user is logged in
			if (!$this->session->set_userdata('userID', $userID)) {
				return 'false';
			} else {
				return $user;
			}
        }
    }

    function logout($userID) {
        $this->session->unset_userdata('userID');
    }

    function checkCredentials($username, $password) {
        if (!$username = trim($username)) return false;
        if (!$password = trim($password)) return false;

        $this->db->where("username", $username);
        $this->db->where("password", md5($password));
        $this->db->where("active", "true");

        $userQuery = $this->db->get("brim_users");
        $matchesNo = $userQuery->num_rows();

        if ($matchesNo == 1) {
            return $userQuery->row();
        } else {
            return false;
        }
    }

    function getUserByID($userID) {
        $this->db->where("userID", $userID);
        $query = $this->db->get('brim_users');
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    function getCurrentUser() {
		if ($userID = $this->session->userdata('userID')) {
			$userID = intval($userID);
			if ($user = $this->getUserByID($userID)) return $user;
			else return false;
		} else {
			return false;
		}
	}

	/**
	 * Files do not process on holidays or weekends
	 */
	function isHolidayOrWeekend() {
		$todayDate = date("Y-m-d", strtotime('today'));
		$this->db->where('dateHoliday',$todayDate);
		$this->db->where('activeHoliday', 1);
		$query = $this->db->get('brim_holidays');
		if ($query->num_rows() > 0) {
			return true;
		} else {
			$dayOfWeek = date('w', strtotime($todayDate));
			if ($dayOfWeek == 0 || $dayOfWeek == 6) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Log user activity
	 */
	function logUserActivity($idUser, $action, $query) {

		$data = array(
			'idUser' => $idUser,
			'actionLog' => $action,
			'queryLog' => $query
		);

		$this->db->insert('brim_user_log', $data);
	}
}

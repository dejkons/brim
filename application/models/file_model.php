<?php

class File_model extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();
	}

	function GetAllFiles($currentPage, $numPerPage, $filter) {
		if ($currentPage <= 0) {
			$currentPage = 1;
		}
		$from = ($currentPage - 1) * $numPerPage;

		$this->db->start_cache();
		if ($filter['filterFileName'] != '') $this->db->where('fileName LIKE', "%" . $filter['filterFileName'] . "%");
		if ($filter['filterFileDateFrom'] != '') $this->db->where('fileTimestamp >=', $filter['filterFileDateFrom']);
		if ($filter['filterFileDateTo'] != '') $this->db->where('fileTimestamp <=', $filter['filterFileDateTo']);
		$this->db->stop_cache();

		$this->db->order_by('timestamp', 'desc');

		if ($numPerPage > 0) {
			$this->db->limit($numPerPage, $from);
		}

		$result = $this->db->get('brim_file_log')->result();

		//COUNT ALL FILTERED FILES
		$result2 = $this->db->count_all_results('brim_file_log');
		$this->db->flush_cache();

		return array($result, $result2);
	}

	function logFile($fileName, $fileSize, $fileTimestamp, $encryptionStatus, $location) {
		// first checking is it already logged
		$this->db->where("fileName", $fileName);
		$this->db->where("fileSize", $fileSize);
		//$this->db->where("fileTimestamp", $fileTimestamp);
		$this->db->where("encryptionStatus", $encryptionStatus);
		$this->db->where("fileLocation", $location);
		$this->db->limit(1);
		$result = $this->db->count_all_results('brim_file_log');

		if ($result == 1) {
			return false;
		} else {

			$data = array(
				"fileName" => $fileName,
				"fileSize" => $fileSize,
				"fileTimestamp" => $fileTimestamp,
				"encryptionStatus" => $encryptionStatus,
				"fileLocation" => $location
			);

			$this->db->insert('brim_file_log', $data);
			return true;
		}
	}

	function getLocationById($locationId) {
		$this->db->where("endpointActive", 1);
		$this->db->where("endpointId", $locationId);
		$query = $this->db->get('brim_sftp_endpoints');
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function getLocationList() {
		$this->db->where("endpointActive", 1);
		$query = $this->db->get('brim_sftp_endpoints');
		$res = array();
		foreach ($query->result() as $row) {
			$res[$row->endpointId] = $row->endpointDirectory;
		}

		return $res;
	}
}

?>

<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	private $itemsPerPageEventsValues;
	private $itemsPerPage;
    
    public function __construct() {
        parent::__construct();
		$this->load->model("user_model");
		$this->load->model("file_model");
		$this->setDefaults();
		if (!$this->user_model->getCurrentUser()) {
			header("Location: " . $this->config->item("base_url") . "/logout");
		} else {
			// log what you load
			$loggedInUser = $this->user_model->getCurrentUser();
			$this->user_model->logUserActivity($loggedInUser->userID, 'view', $_SERVER['REQUEST_URI']);
		}
    }

	public function setDefaults() {
		$this->itemsPerPageEventsValues = array(10, 50, 100);
		if ($currentItemsPerPageValue = $this->session->userdata("itemsPerPageClients")) $this->itemsPerPage = $currentItemsPerPageValue;
		else $this->itemsPerPage = $this->itemsPerPageEventsValues[0];
	}

	public function setNewItemsPerPage($itemsPerPage = 0) {
		$itemsPerPage = intval($itemsPerPage);
		if (in_array($itemsPerPage, $this->itemsPerPageEventsValues, true)) {
			$this->session->set_userdata('itemsPerPageClients', $itemsPerPage);
		} else {
			$this->session->set_userdata('itemsPerPageClients', $this->itemsPerPageEventsValues[0]);
		}
	}

    public function index($currentPage = 1) {

		if ($currentPage == 0) $currentPage = 1;

        $data['title'] = $this->title.' - Files Log';
		$data['base_url'] = $this->config->item("base_url");

		$currentUser = $this->user_model->getCurrentUser();
		$data['currentUser'] = $currentUser;
		$data['currentPage'] = $this->input->get('hdPageNumber', true);

		if ($this->input->get('resetPagination') == 'true') {
			$data['currentPage'] = 1;
		}

		if ($data['currentPage'] == '') $data['currentPage'] = $currentPage;
		$data['numPerPage'] = $this->itemsPerPage;
		if ($data['numPerPage'] > 100) {
			$data['numPerPage'] = 100;
		}
		$this->setNewItemsPerPage($data['numPerPage']);
		$data['perPage'] = $data['numPerPage'];

		//// List of filters
		$filter = array();
		$filter['filterFileName'] = $this->input->get('filterFileName', true);
		$filter['filterFileDateFrom'] = $this->input->get('filterFileDateFrom', true);
		$filter['filterFileDateTo'] = $this->input->get('filterFileDateTo', true);

		if ($filesTemp = $this->file_model->GetAllFiles($data['currentPage'], $data['numPerPage'], $filter)) {
			$fileList = $filesTemp[0];
			$filesNum = $filesTemp[1];
		} else {
			$fileList = false;
			$filesNum = 0;
		}

		$data['filesList'] = $fileList;
		$data['locationList'] = $this->file_model->getLocationList();

		$data['numberOfPages'] = ceil($filesNum / $data['numPerPage']);
		if ($data['currentPage'] > $data['numberOfPages']) {
			$data['currentPage'] = $data['numberOfPages'];
		}
		if ($data['numberOfPages'] <= 1) $data['hideNavigation'] = "display_none"; else $data['hideNavigation'] = "";

		$data['startingPoint'] = (($data['currentPage'] - 1) * $data['numPerPage']) + 1;

		$imageLeft = "";
		$imageRight = "";
		if ($data['currentPage'] == 1) {
			$imageLeft = "_grey";
			$imageRight = "";
		}
		if ($data['currentPage'] == $data['numberOfPages']) {
			$imageLeft = "";
			$imageRight = "_grey";
		}
		$data['imageLeft'] = $imageLeft;
		$data['imageRight'] = $imageRight;

		$data = array_merge($data, $filter);

		$currentUrl = $this->config->item('base_url') . '/home/' . $data['currentPage'] . '/';
		$data['actionUrl'] = $currentUrl;
		if ($currentQueryStr = $_SERVER['QUERY_STRING']) $currentUrl .= '?' . $currentQueryStr;
		$data['currentUrl'] = $currentUrl;
		$data['currentQueryStr'] = $currentQueryStr;

        $this->load->view('home_view', $data);
    }

	public function createExcel($name, $from, $to, $uploadDirectory = "upload") {
		// log report creation
		$loggedInUser = $this->user_model->getCurrentUser();
		$this->user_model->logUserActivity($loggedInUser->userID, 'report', $_SERVER['REQUEST_URI']);

		$fileNameNoExt = 'file_log_'.rand(0,100000)."_".date("Ymd");
		$ext = ".xlsx";
		$fileName = $fileNameNoExt.$ext;
		$filter = array();
		$filter['filterFileName'] = $name == "null" ? "" : $name;
		$filter['filterFileDateFrom'] = $from;
		$filter['filterFileDateTo'] = $to;

		if ($filesTemp = $this->file_model->GetAllFiles(0, 0, $filter)) {
			$fileList = $filesTemp[0];
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Id');
		$sheet->setCellValue('B1', 'File Name');
		$sheet->setCellValue('C1', 'File Size');
		$sheet->setCellValue('D1', 'Encryption Status');
		$sheet->setCellValue('E1', 'File Location');
		$sheet->setCellValue('F1', 'File Timestamp');
		$sheet->setCellValue('G1', 'Timestamp');
		$rows = 2;
		foreach ($fileList as $val){
			$sheet->setCellValue('A' . $rows, $val->id);
			$sheet->setCellValue('B' . $rows, $val->fileName);
			$sheet->setCellValue('C' . $rows, $val->fileSize);
			$sheet->setCellValue('D' . $rows, $val->encryptionStatus);
			$sheet->setCellValue('E' . $rows, $val->fileLocation);
			$sheet->setCellValue('F' . $rows, $val->fileTimestamp);
			$sheet->setCellValue('G' . $rows, $val->timestamp);
			$rows++;
		}
		$writer = new Xlsx($spreadsheet);
		$writer->save($uploadDirectory."/".$fileName);
		header("Content-Type: application/vnd.ms-excel;");
		echo $fileName;
	}

	public function createExcelDaily() {
		$searchName = "null";
		$todayDate = date("Y-m-d", strtotime('today'));
		$tomorrowDate = date("Y-m-d", strtotime('tomorrow'));
		$uploadDirectory = "uploadDaily";
		Home::createExcel($searchName, $todayDate, $tomorrowDate, $uploadDirectory);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

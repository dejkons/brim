<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use phpseclib3\Net\SFTP;
use phpseclib3\Net\SFTP\Stream;

class File extends MY_Controller {

	private $startingLocationId = 1;

	public function __construct() {
		parent::__construct();
		$this->load->model("file_model");
		$this->load->model("user_model");
	}

	/**
	 * Return resource\$location, $username, $password
	 */
	public function logIntoSFTP($host, $user, $pass) {
		$sftp = new SFTP($host);
		$sftp->login($user, $pass);

		return $sftp;
	}

	/**
	 * Method for encryption for testing purposes
	 */
	private function decryptPGP($file, $key, $passPhrase) {
		$textToDecrypt = readfile($file);
		$res = gnupg_init();
		gnupg_adddecryptkey($res,$key,$passPhrase);
		return gnupg_decrypt($res,$textToDecrypt);
	}

	/**
	 * Method for checking file integrity
	 */
	private function isKeptFileIntegrity($sftpResource, $file1, $file2) {
		$textIn  = $sftpResource->get($file1);
		$textOut = $sftpResource->get($file2);

		if ($textIn != $textOut) {
			return false;
		}

		$sizeIn  = $sftpResource->filesize($file1);
		$sizeOut = $sftpResource->filesize($file2);

		if ($sizeIn != $sizeOut) {
			return false;
		}

		return true;
	}

	/**
	 * It can easily execute once at 10pm for the alert sake
	 * but also can be executed at 11pm just as the last minute try
	 */
	public function scanPartnerEntryLocations() {

		$expectedFileNameSuffix = date("Ymd");
		$escalationEmail = "monitoring@brimfinancial.com";
		$decryptKey = "8660281B6051D071D94B5B230549F9DC851566DC";

		// first check is it weekend or holiday
		if ($this->user_model->isHolidayOrWeekend()) {
			die("No processing files on holiday or weekend");
		}

		// define all locations needed here
		$partnerEntryLocationDirObj = $this->file_model->getLocationById($this->startingLocationId);
		$partnerEntryLocationDir = $partnerEntryLocationDirObj->endpointDirectory;

		$partnerOutputLocationDirObj = $this->file_model->getLocationById($partnerEntryLocationDirObj->nextEndpointId);
		$partnerOutputLocationDir = $partnerOutputLocationDirObj->endpointDirectory;

		$sasInputLocationDirObj = $this->file_model->getLocationById($partnerOutputLocationDirObj->nextEndpointId);
		$sasInputLocationDir = $sasInputLocationDirObj->endpointDirectory;

		// login to SFTP
		$sftp = File::logIntoSFTP($partnerEntryLocationDirObj->endpointHost, $partnerEntryLocationDirObj->endpointUsername,
			$partnerEntryLocationDirObj->endpointPassword);

		// check for all 12 files are present
		$entryLocationFiles = $sftp->nlist($partnerEntryLocationDir);

		for ($i=1; $i<=12; $i++) {
			if (!in_array("File".$i.".".$expectedFileNameSuffix, $entryLocationFiles)) {
				// if more than 10pm, notify someone
				$now = new DateTime();
				$thresholdTime = new DateTime();
				$thresholdTime->setTime('22', '00', '00' );

				// if it is past 10pm and missing something, escalate, otherwise proceed
				if ($i >= 1 && $i <= 7 && $thresholdTime < $now) {
					// send escalation email
					mail($escalationEmail,"Missing files after 10pm", "Missing files after 10pm");
				}
				break;
			}
		}

		// now when all files are on sftp, log them. For the cron job sake, stop if files are logged already
		for ($i=1; $i<=12; $i++) {
			$fileName = "File".$i.".".$expectedFileNameSuffix;
			$fileSize = $sftp->filesize($partnerEntryLocationDir."/".$fileName);
			$fileTimeStamp = $sftp->filemtime($partnerEntryLocationDir."/".$fileName);
			$encryptionStatus = "encrypt";
			$location = 1;

			$this->file_model->logFile($fileName, (int)$fileSize, date('Y-m-d H:i:s', $fileTimeStamp), $encryptionStatus, $location);
			// moving files to P1 outgoing
			$sftp->put($partnerOutputLocationDir.'/'.$fileName, $sftp->get($partnerEntryLocationDir.'/'.$fileName));
		}

		// Now all files are on P1 output, proceeding further
		$temp = '';
		Stream::register();
		for ($i=1; $i<=12; $i++) {
			$fileName = "File".$i.".".$expectedFileNameSuffix;
			// file integrity tests
			if (!File::isKeptFileIntegrity($sftp, $partnerEntryLocationDir.'/'.$fileName, $partnerOutputLocationDir.'/'.$fileName )) {
				mail($escalationEmail,"Partner Entry discrepancy", "Partner Entry location incoming and outgoing files differ in content or size");
				break;
			}

			// decrypt files (PGP) - missing key for decryption, not mentioned in assignment
			//File::decryptPGP($partnerOutputLocationDir.'/'.$fileName, $decryptKey, "test");

			// merge $i = 1 .. 7 into one file, rename others
			if ($i >= 1 && $i < 8) {
				$fp = fopen('sftp://dejan:Dejkons1@@@@@@@127.0.0.1:22/'.$partnerOutputLocationDir . '/' . $fileName, 'r');
				while (!feof($fp)) {
					$temp.= fread($fp, 1024);
				}
				if ($i == 7) {
					$sftp->put($partnerOutputLocationDir . '/FileA.'.$expectedFileNameSuffix, $temp);
				}
			} else if ($i == 8) {
				$sftp->rename($partnerOutputLocationDir.'/'.$fileName, $partnerOutputLocationDir.'/FileB.'.$expectedFileNameSuffix);
			} else if ($i == 9) {
				$sftp->rename($partnerOutputLocationDir.'/'.$fileName, $partnerOutputLocationDir.'/FileC.'.$expectedFileNameSuffix);
			} else if ($i == 10) {
				$sftp->rename($partnerOutputLocationDir.'/'.$fileName, $partnerOutputLocationDir.'/FileD.'.$expectedFileNameSuffix);
			} else if ($i == 11) {
				$sftp->rename($partnerOutputLocationDir.'/'.$fileName, $partnerOutputLocationDir.'/FileE.'.$expectedFileNameSuffix);
			} else if ($i == 12) {
				$sftp->rename($partnerOutputLocationDir.'/'.$fileName, $partnerOutputLocationDir.'/FileF.'.$expectedFileNameSuffix);
			}
		}

		// login to SAS SFTP
		$sftp_sas = File::logIntoSFTP($sasInputLocationDirObj->endpointHost, $sasInputLocationDirObj->endpointUsername,
			$sasInputLocationDirObj->endpointPassword);

		// now when all files are on sftp, log them. For the cron job sake, stop if files are logged already
		$arrayOfExpectedSasFilesOnSFTP = ['FileA', 'FileB', 'FileC', 'FileD', 'FileE', 'FileF'];
		foreach ($arrayOfExpectedSasFilesOnSFTP as $sasFileName) {
			$fileName = $sasFileName.".".$expectedFileNameSuffix;
			$fileSize = $sftp->filesize($partnerOutputLocationDir."/".$fileName);
			$fileTimeStamp = $sftp->filemtime($partnerOutputLocationDir."/".$fileName);
			$encryptionStatus = "decrypt";
			$location = 2;

			$this->file_model->logFile($fileName, (int)$fileSize, date('Y-m-d H:i:s', $fileTimeStamp), $encryptionStatus, $location);
			// moving files to SAS input server
			$sftp_sas->put($sasInputLocationDir.'/'.$fileName, $sftp->get($partnerOutputLocationDir.'/'.$fileName));
		}

	}

	/**
	 * Must be a CRON job, it doesn't know when SAS script is done,
	 * must check often.
	 * Problem is outgoing location because missing files can be for two reason: SAS script not yet executed or
	 * problem occurred. That is why the best to execute check closer to midnight
	 * This implementation is done under the assumption that files after SAS processing are keeping the name
	 * We need file name in order to find out what we are searching for?
	 */
	public function scanPartnerSASLocations() {
		// assumption because that info is missing about file names on SAS
		$expectedFileNameSuffix = date("Ymd");
		$escalationEmail = "monitoring@brimzddfinancial.com";
		$sasInputLocationDirId  = 3;
		$sasOutputLocationDirId = 4;

		// first check is it weekend or holiday
		if ($this->user_model->isHolidayOrWeekend()) {
			die("No processing files on holiday or weekend");
		}

		// define all parameters needed here
		$sasInputLocationDirObj = $this->file_model->getLocationById($sasInputLocationDirId);
		$sasInputLocationDir = $sasInputLocationDirObj->endpointDirectory;

		$sasOutputLocationDirObj = $this->file_model->getLocationById($sasOutputLocationDirId);
		$sasOutputLocationDir = $sasOutputLocationDirObj->endpointDirectory;

		// login to SFTP
		$sftp_input  = File::logIntoSFTP($sasInputLocationDirObj->endpointHost, $sasInputLocationDirObj->endpointUsername,
			$sasInputLocationDirObj->endpointPassword);
		$sftp_output = File::logIntoSFTP($sasOutputLocationDirObj->endpointHost, $sasOutputLocationDirObj->endpointUsername,
			$sasOutputLocationDirObj->endpointPassword);

		$arrayOfExpectedSasFilesOnSFTP = ['FileA', 'FileB', 'FileC', 'FileD', 'FileE', 'FileF'];

		foreach ($arrayOfExpectedSasFilesOnSFTP as $sasFileName) {
			// input SAS endpoint
			$fileName = $sasFileName.".".$expectedFileNameSuffix;
			if ($sftp_input->is_file($sasInputLocationDir."/".$fileName)) {
				$fileSize = $sftp_input->filesize($sasInputLocationDir."/".$fileName);
				$fileTimeStamp = $sftp_input->filemtime($sasInputLocationDir."/".$fileName);
				$encryptionStatus = "encrypt";
				$location = 3;

				$this->file_model->logFile($fileName, (int)$fileSize, date('Y-m-d H:i:s', $fileTimeStamp), $encryptionStatus, $location);
			} else {
				// escalate since file is missing
				mail($escalationEmail,"Missing file", "Missing file");
			}

			if ($sftp_output->is_file($sasOutputLocationDir."/".$fileName)) {
				$fileSize = $sftp_output->filesize($sasOutputLocationDir . "/" . $fileName);
				$fileTimeStamp = $sftp_output->filemtime($sasOutputLocationDir . "/" . $fileName);
				$str = "decrypt";
				$encryptionStatus = $str;
				$location = 4;

				$this->file_model->logFile($fileName, (int)$fileSize, date('Y-m-d H:i:s', $fileTimeStamp), $encryptionStatus, $location);
			} else {
				mail($escalationEmail,"Missing files", "Missing files");
			}
		}
	}

	/**
	 * Must be CRON job, it doesn't know when scripts download to P2 server
	 * must check often
	 */
	public function scanPartnerExitLocations() {
		// assumption because that info is missing about file names on SAS
		$expectedFileNameSuffix = date("Ymd");
		$escalationEmail = "monitoring@brimzddfinancial.com";
		$partnerExitInputLocationDirId  = 5;
		$partnerExitOutputLocationDirId = 6;

		// first check is it weekend or holiday
		if ($this->user_model->isHolidayOrWeekend()) {
			die("No processing files on holiday or weekend");
		}

		// define all parameters needed here
		$partnerExitInputLocationDirObj = $this->file_model->getLocationById($partnerExitInputLocationDirId);
		$partnerExitInputLocationDir = $partnerExitInputLocationDirObj->endpointDirectory;

		$partnerExitOutputLocationDirObj = $this->file_model->getLocationById($partnerExitOutputLocationDirId);
		$partnerExitOutputLocationDir = $partnerExitOutputLocationDirObj->endpointDirectory;

		// login to SFTP
		$sftp_input  = File::logIntoSFTP($partnerExitInputLocationDirObj->endpointHost, $partnerExitInputLocationDirObj->endpointUsername,
			$partnerExitInputLocationDirObj->endpointPassword);
		$sftp_output = File::logIntoSFTP($partnerExitOutputLocationDirObj->endpointHost, $partnerExitOutputLocationDirObj->endpointUsername,
			$partnerExitOutputLocationDirObj->endpointPassword);

		$arrayOfExpectedSasFilesOnSFTP = ['FileA', 'FileB', 'FileC', 'FileD', 'FileE', 'FileF'];

		foreach ($arrayOfExpectedSasFilesOnSFTP as $sasFileName) {
			// input exit endpoint
			$fileName = $sasFileName.".".$expectedFileNameSuffix;
			if ($sftp_input->is_file($partnerExitInputLocationDir."/".$fileName)) {
				$fileSize = $sftp_input->filesize($partnerExitInputLocationDir."/".$fileName);
				$fileTimeStamp = $sftp_input->filemtime($partnerExitInputLocationDir."/".$fileName);
				$encryptionStatus = "encrypt";
				$location = 5;

				$this->file_model->logFile($fileName, (int)$fileSize, date('Y-m-d H:i:s', $fileTimeStamp), $encryptionStatus, $location);
			} else {
				// escalate since file is missing
				mail($escalationEmail,"Missing file", "Missing file");
			}

			if ($sftp_output->is_file($partnerExitOutputLocationDir."/".$fileName)) {
				$fileSize = $sftp_output->filesize($partnerExitOutputLocationDir . "/" . $fileName);
				$fileTimeStamp = $sftp_output->filemtime($partnerExitOutputLocationDir . "/" . $fileName);
				$encryptionStatus = "decrypt";
				$location = 6;

				$this->file_model->logFile($fileName, (int)$fileSize, date('Y-m-d H:i:s', $fileTimeStamp), $encryptionStatus, $location);
			} else {
				mail($escalationEmail,"Missing files", "Missing files");
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	public $TAG_REQUEST_CODE = "requestCode";
	public $TAG_RESULT_CODE = "resultCode";

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
	public function checkin()
	{
		//$request = getRequestData();
		$request = $this->createDummyCheckinRequest();
		$this->setRequestCodeHeaderToResponse();
		//Extract: build data key,value pairs for inserting
		$data = array();
		$data["staff_id"] = $request->staffId;
		$data["date"] = $request->date;
		$data["time_in"] = $request->timeIn;
		
		//load Attendance model
		$this->load->model('AttendanceModel');
		$attendance = $this->AttendanceModel->isCheckedInAlready($request->staffId, $request->date);
		//var_dump($attendance);
		if($attendance != null)
		{
			$this->setResultCode(101);
			$response["msg"] = "You have already checked in at " .$attendance[0]->time_in;
		}
		else
		{
			$attendanceId = $this->AttendanceModel->checkin($request->staffId, $request->date, $request->shiftId, $request->timeIn);	
			echo "\nattendanceId ". $attendanceId;
			if($attendanceId > 0)
			{
				$this->setResultCode(100);
				$response["msg"] = "Attendance registered Successfully.";
			}
			else
			{
				$this->setResultCode(102);
				$response["msg"] = "Error 102: Something went wrong. Try again or report the issue to admin";
			}
		} 
		echo json_encode($response);	
	}
	
	public function checkout()
	{
		//$request = getRequestData();
		$request = $this->createDummyCheckoutRequest();
		$this->setRequestCodeHeaderToResponse();
		//Extract: build data key,value pairs for inserting
		$response = array();
		
		//load Attendance model
		$this->load->model('AttendanceModel');
		$attendance = $this->AttendanceModel->isCheckedInAlready($request->staffId, $request->date);
		
		if($attendance != null)
		{
			$success = $this->AttendanceModel->checkout($request->staffId, $request->date, $request->shiftId, $request->timeOut);
			echo "\nsuccess :". $success;
			if($success >= 0)
			{
				$this->setResultCode(100);
				$response["msg"] = "checked out Successfully.";
			}
			else
			{
				
			}
		}
		else 
		{	
			$attendance = $this->AttendanceModel->insertCheckout($request->staffId, $request->date, $request->timeOut);
			echo "\nattendanceId ". $attendanceId;
			if($attendanceId > 0)
			{
				$this->setResultCode(103);
				$response["msg"] = "Checked out successfully, you haven't checked in today. Please contact administrator";
			}
			else
			{
				
			}
			
		}
		echo json_encode($response);
	}
	
	/* creates dummy checkin request */
	private function createDummyCheckinRequest()
	{
		$_SERVER[$this->TAG_REQUEST_CODE] = "100";
		$request = array();
		$request["staffId"] = 6;
		$request["shiftId"] = 1;
		$request["date"] = date("Y-m-d"); //"00-00-0000";
		$request["timeIn"] = date("Y-m-d H:i:s");//"08:00:00";
		var_dump($request);
		return Json_decode(json_encode($request));
	}
	
	/* creates dummy checkin request */
	private function createDummyCheckoutRequest()
	{
		$_SERVER[$this->TAG_REQUEST_CODE] = "101";
		$request = array();
		$request["staffId"] = 6;
		$request["shiftId"] = 1;
		$request["date"] = date("Y-m-d"); //"00-00-0000";
		$request["timeOut"] = date("Y-m-d H:i:s");//"08:00:00";
		var_dump($request);
		return Json_decode(json_encode($request));
	}
	
	private function getRequestData()
	{
		$postdata = file_get_contents("php://input");
		return json_decode($postdata);
	}
	
	private function setRequestCodeHeaderToResponse()
	{
		header("$this->TAG_REQUEST_CODE: " . 100 . "");
	}
	private function setResultCode($resultCode)
	{				
		$this->output->set_header(''.$this->TAG_RESULT_CODE .': '. $resultCode .'');
		
	}
	
}

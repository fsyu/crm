<?php 
ob_start();
session_start();

class CrmController extends Zend_Controller_Action
{
	protected $db;
	protected $user;
	protected $useragent;
    public function init()
    {
		$this->view->module = $this->_request->getModuleName();
		$this->view->controller = $this->_request->getControllerName();
		$this->view->action = $this->_request->getActionName();
		$this->db = Zend_Controller_Front::getInstance()
		->getParam("bootstrap")
		->getPluginResource("db")
		->getDbAdapter();
		
	//	$auth = Zend_Auth::getInstance();
		//$this->_helper->layout()->setLayout('m_layout');
		
    }

	 public function indexAction() {



		$type    		= $_POST['action']; 		// 做什麼事情
		if($type == null){
		    $type    		= $_GET['action']; 		// 做什麼事情
		}
				
		$step    		= $_POST['step']; 		// 做什麼事情
		if($step == null){
			$step    		= $_GET['step']; 		// 做什麼事情
		}

		if(!isset($_SESSION['school_year'])){
		    $school_year = $_POST['school_year']; 		// 做什麼事情
		    if($school_year == null){
		    	$school_year = $_GET['school_year']; 		// 做什麼事情
		    }
		    
		    $_SESSION['school_year'] = $school_year;
		}else{
		    $school_year = $_SESSION['school_year'];
		}

		
		if(!isset($_SESSION['semester'])){
		    $semester = $_POST['semester']; 		// 做什麼事情
		    if($semester == null){
		    	$semester = $_GET['semester']; 		// 做什麼事情		    	
		    }
		    $_SESSION['semester'] = $semester;
		}else{
		    $semester = $_SESSION['semester'];
		}
		
		$school_year = 105;
		$semester = 2;
		$department     = '電子工程系'; 
				//科系
	
		////////////////////////step1/////////////////////////
		//os
		
		$SQLCommand = "SELECT id, name FROM os ORDER BY name";
		$result = $this->db->query($SQLCommand,array());
		$r = $result->fetchAll();
		$this->view->os = $r;
		
		//software
		$SQLCommand = "SELECT id, name FROM software ";
		$result = $this->db->query($SQLCommand,array());
		$r = $result->fetchAll();
		$this->view->software = $r;	
		
		//computer_room
		$SQLCommand = "SELECT *
						FROM computer_room
						WHERE school_year=?
						AND semester = ?
						AND department = ?";
		$result = $this->db->query($SQLCommand,array($school_year, $semester, $department ));
		$r = $result->fetchAll();
		$this->view->computer_room = $r;

		////////////////////////step2/////////////////////////
		//if($step=='step2'){
    		$computer_room_id = $_GET['roomid']; 
    
    		//lecture
    		$SQLCommand = "SELECT * FROM lecture where computer_room_id=?";
    		$result = $this->db->query($SQLCommand,array($computer_room_id));
    		$r = $result->fetchAll();
    		$this->view->lecture_list = $r;
		//}

		///////////////////////step3/////////////////////////

		//os
		$SQLCommand = "SELECT a.id, a.computer_room_id, o.name as os_name 
						FROM computer_room_os a
						JOIN os o ON o.id = a.os_id";
		$result = $this->db->query($SQLCommand,array());
		$r = $result->fetchAll();
		$this->view->os_list = $r;		
		
		//software
		$SQLCommand = "SELECT b.id, b.computer_room_id ,s.`name` as software_name
						FROM computer_room_software b
						JOIN software s ON s.id = b.software_id";
		$result = $this->db->query($SQLCommand,array());
		$r = $result->fetchAll();
		$this->view->software_list = $r;		
		
		//lecture
		$SQLCommand = "SELECT * FROM lecture";
		$result = $this->db->query($SQLCommand,array());
		$r = $result->fetchAll();
		$this->view->lecture = $r;

		/////////////////////////////////////////

		switch ($type) {
				
			case '新增教室': 
				/////////////////////////新增電腦教室/////////////////////////
				$room_num 	 	= $_POST['room_num'];		//教室編號
				$room_name 		= $_POST['room_name'];		//教室名稱
				$pc_num     	= $_POST['pc_num']; 		//個人電腦數量
				$sever_num 		= $_POST['sever_num'];		//伺服器數量
				$os				= $_POST['os'];				//作業系統
				$software		= $_POST['software'];		//軟體
				
				$SQLCommand="INSERT INTO computer_room (school_year,semester,department,room_num, room_name, pc_num, sever_num) 
							VALUES (?,?,?,?,?,?,?)";
				$result = $this->db->query($SQLCommand,array($school_year,$semester,$department,$room_num, $room_name, $pc_num, $sever_num));
				$id = $this->db->lastInsertId(); //取得最後的id
			
				if(!empty($_POST['os'])) {
					foreach($_POST['os'] as $check) {
						$SQLCommand="INSERT INTO computer_room_os (computer_room_id, os_id) VALUES (?,?)";
						$result = $this->db->query($SQLCommand,array($id, $check));
					}
				}		
				
				if(!empty($_POST['software'])) {
					foreach($_POST['software'] as $check) {
						$SQLCommand="INSERT INTO computer_room_software (computer_room_id, software_id) VALUES (?,?)";
						$result = $this->db->query($SQLCommand,array($id, $check));
					}
				}
				$this->redirect('crm/index?step=step1');
				break;

			case '新增課程': 
				$rid 		= $_POST['rid'];		//教室編號
				$lesson_name 	= $_POST['lesson_name'];	//班級名稱
				$student_num 	= $_POST['student_num'];	//修課人數
				$hour     		= $_POST['hour']; 			//每週上課時數
				$teacher_name 	= $_POST['teacher_name'];	//任課老師
				$lecture_name	= $_POST['lecture_name'];	//課程名稱
				$school_code	= $_POST['school_code'];	//學制代號

				$sql="INSERT INTO lecture ( computer_room_id, lesson_name, student_num, hour, teacher_name, lecture_name, school_code) VALUES (?,?,?,?,?,?,?)";
				$result = $this->db->query($sql, array($rid,$lesson_name, $student_num, $hour, $teacher_name, $lecture_name, $school_code));
				$this->redirect('crm/index?step=step2&roomid='.$rid);
				break;

			case "匯出EXCEL":
				$this->view->ExportComputerExcel();
				break;
			case "delectLecture":
			   $this->delectLecture();
			default:	
				break;
			}	
	 }

	public function delectLecture(){
	    $id  = $_GET['id'];
	    $roomid  = $_GET['roomid'];
	    $sql="delete from lecture where id = ?";
	    $result = $this->db->query($sql, array($id));
	    $this->redirect('crm/index?step=step2&roomid='.$roomid);
	    
	}
	
	public function osAction() {
		//os
		$SQLCommand = "SELECT * FROM os ORDER BY name";
		$result = $this->db->query($SQLCommand,array());
		$r = $result->fetchAll();
		$this->view->os = $r;		
		
		//software
		$SQLCommand = "SELECT * FROM software ORDER BY name";
		$result = $this->db->query($SQLCommand,array());
		$r = $result->fetchAll();
		$this->view->software = $r;
		
		$type			= $_POST['action']; 		// 做什麼事情
		$os_id			= $_POST['os_id'];			// 作業系統ID
		$os_name		= $_POST['os_name']; 		// 作業系統NAME
		$valid			= $_POST['valid']; 			// XXXX
		$software_id	= $_POST['software_id'];	// 軟體ID
		$software_name	= $_POST['software_name']; 	// 軟體NAME
		
		switch ($type) {
			case "新增作業系統":

				$sql="INSERT INTO os(name,valid) VALUES (?,?);";
				$result = $this->db->query($sql, array($os_name,$valid));
				$this->redirect('crm/os');
	
				break;
			
			case "刪除作業系統":
				
				$sql="DELETE FROM os WHERE id=?;";
				$result = $this->db->query($sql, array($os_id));
				$this->redirect('crm/os');
				
				break;
			
			case "新增教學軟體":

				$sql="INSERT INTO software(name) VALUES (?);";
				$result = $this->db->query($sql, array($software_name));
				$this->redirect('crm/os');
	
				break;
			
			case "刪除教學軟體":
				
				$sql="DELETE FROM software WHERE id=?;";
				$result = $this->db->query($sql, array($software_id));
				$this->redirect('crm/os');
				
				break;
			
			default:	

				break;
		}
	}	
	

	public function chartAction() {
		
		$type 			= $_POST['action']; 		// 做什麼事情
		$school_year 	= $_POST['school_year'];	// 學年
		$semester 		= $_POST['semester']; 		// 學期

		switch ($type) {
				
			case '查詢':
				
				$SQLCommand = "SELECT c.id, school_year, semester, department, room_num, room_name, pc_num, COUNT(computer_room_id) as lecture_num, SUM(student_num) as total_s_num , SUM(hour)as total_hour, ROUND((SUM(student_num)/pc_num),1)as avg_s_num, ROUND(SUM(hour)/62*100,2) as avg_hour 
					FROM computer_room c
					JOIN lecture l ON c.id = l.computer_room_id 
					WHERE school_year = ?
					AND semester = ?
					GROUP BY room_num
					ORDER BY department, room_num";
						
				$result = $this->db->query($SQLCommand,array($school_year,$semester));
				$r = $result->fetchAll();
				$this->view->chart = $r;


				break;
			default:	
				break;
			}		

	}		
}

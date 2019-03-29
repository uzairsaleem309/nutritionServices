<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api extends REST_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	//this service is used for users log-in
	public function userLogin_post()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if (!$method)
		{
			$data = array('Status' => 400 , 'Message' => "Bad Request" );
			echo json_encode($data);
		}
		else
		{
			$login_data = array(
				'user_username'=> $this->post('user_username'),
				'user_password'=> $this->post('user_password')
			);
			$result = array();
			//conditions to check if fields are not empty
			if(!$this->post('user_username') && !$this->post('user_password')) {
				echo json_encode(['status' => FALSE, 'message' => 'Username and Password cannot be empty', 'data' => $result], 400);
				exit;
			} else if(!$this->post('user_username')) {
				echo json_encode(['status' => FALSE, 'message' => 'Username cannot be empty', 'data' => $result], 200);
				exit;
			} else if(!$this->post('user_password')) {
				echo json_encode(['status' => FALSE, 'message' => 'Password cannot be empty', 'data' => $result], 200);
				exit;
			}

			$result = $this->User_model->userlogin();
			if($result)
			{
				echo json_encode(['status' => TRUE, 'message' => 'Logged in successfully', 'data' => $result], 200);
			}
			else {
				$result = array();
				echo json_encode(['status' => FALSE, 'message' => 'Username or Password is incorrect', 'data' => $result], 200);
			}
		}
	}

	//this services is used for new users registeration
	function userRegisteration_post() 
	{
		$data = array(
			'user_username'=> $this->post('user_username'),
			'user_password'=> $this->post('user_password'),
			'user_age'=>$this->post('user_age')
		);

		//first we check any user exist with the same username and we show a message accordingly
		if($this->User_model->userexist($data))
		{
			echo json_encode(['status' => FALSE, 'message' => 'User with same username already exist'], 200);
		}
		else
		{
                        $result = $this->User_model->registerUser($data);
			if($result){
				echo json_encode(['status' => TRUE, 'message' => 'Registered successfully','data'=>$result], 200);
			}
			else
			{
				echo json_encode(['status' => FALSE, 'message' => 'Registeration Failed'], 200);
			}
		}
		
	}

	//this service is used to insert users diseases
	function addUserDisease_post()
	{
		$diseases = $this->post('userDisease_disease_id');
		$i = 0;
		
			//if a user has more than one disease then from the post request we are sending an array of ud_disease_id
			//then looping over it to add the records
			foreach ($diseases as $row) {
				$data = array(
				'userDisease_user_id' => $this->post('userDisease_user_id'),
				'userDisease_disease_id'=>$diseases[$i]
				);
				if($this->User_model->addDisease($data))
				{
					$i++;
				}
			}
			$userData = array(
				'userDetails_user_id' => $this->post('userDisease_user_id'),
				'userDetails_height' => $this->post('userDetails_height'),
				'userDetails_current_weight' => $this->post('userDetails_current_weight'),
				'userDetails_goal_weight' => $this->post('userDetails_goal_weight')
			);
			
			//checking if all records are inserted
			if($i == count($diseases) && $this->User_model->addUserDetails($userData))
			{
				echo json_encode(['status' => TRUE, 'message' => 'User disease added successfully'], 200);	
			}
			else
			{
				echo json_encode(['status' => TRUE, 'message' => 'User disease could not be added. Please try again'], 200);
			}
		
	}

	//service used to get meals by day and disease
	public function mealPlanByDay_post()
	{
		$data = array(
			'user_id'=>$this->post('user_id'),  
			'day_id'=>$this->post('day_id')
		);

		$result = $this->User_model->getMealPlanByDay($data['user_id'],$data['day_id']);
		if($result)
		{
			$this->response(['status' => TRUE, 'message' => 'Data fetched successfully', 'data' => $result], 200);
		}
		else
		{
			$result = array();
			$this->response(['status' => FALSE, 'message' => 'No record found', 'data' => $result], 200);
		}
	}

	//service used to get exercises by exercise levels
	//exercise level means beginner, intermediate etc
	public function exerciseByLevel_post()
	{
		$data = array(
			'exerciseLevel_id'=>$this->post('exerciseLevel_id')
		);

		$result = $this->User_model->getExerciseByExerciseLevel($data['exerciseLevel_id']);
		if($result)
		{
			$this->response(['status' => TRUE, 'message' => 'Data fetched successfully', 'data' => $result], 200);
		}
		else
		{
			$result = array();
			$this->response(['status' => FALSE, 'message' => 'No record found', 'data' => $result], 200);
		}
	}
}

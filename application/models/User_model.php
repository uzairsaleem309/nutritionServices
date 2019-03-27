<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {

	public function __construct(){
		$this->load->database();
	}
	
	//function used for user's login
	public function userlogin()
	{
		$this->db->select('*');
		$this->db->from('tbl_users as u');
		$this->db->where('user_username', $this->input->post('user_username'));
		$this->db->where('user_password', $this->input->post('user_password'));

		$query = $this->db->get();

        if($query->num_rows() > 0) {
          return $query->row();
        } else {
          return 0;
        }
	}

	//function used to register user's
	public function registerUser($data)
	{
		$query = $this->db->insert('tbl_users',$data);
		if($query)
		{
			$insert_id = $this->db->insert_id();
			//$query = $this->db->query("SELECT * FROM tbl_users WHERE user_id = $insert_id");
			$this->db->where('user_id',$insert_id);
			$this->db->select('*');
			$this->db->from('tbl_users');
			$query1 = $this->db->get();
			return $query1->row();
		}
		else
			return false;
	}

	//function used to add diseases of user
	public function addDisease($data)
	{
		if($this->db->insert('tbl_userDisease',$data))
			return true;
		else
			return false;
	}

	//function used when registering new user to check if user with same username already exist
	public function userexist($userdata)
    {
        $this->db->where(['user_username'=>$userdata['user_username']]);
        $query = $this->db->get('tbl_users');
        if ($query->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    //function to get meals by days and disease
    public function getMealPlanByDay($userId,$dayId)
    {
    	$query = $this->db->query("SELECT u.user_id,u.user_username,d.disease_name,day.day_name,m.mealPlan_menuItem from tbl_users u
		JOIN
		tbl_userdisease ud
		JOIN
		tbl_disease d
		ON
		 ud.userDisease_disease_id = d.disease_id
		JOIN
		tbl_days day
		JOIN
		tbl_mealplan m
		ON
		d.disease_id = m.disease_id
		WHERE m.day_id = '$dayId'
		AND day.day_id = '$dayId'
		AND u.user_id = '$userId'
		AND ud.userDisease_user_id = '$userId'");

		if($query->num_rows() > 0) {
          return $query->result();
        } else {
          return 0;
        }

    }

    //function used to get exercise according to exercise level
    public function getExerciseByExerciseLevel($exerciseLevel_id)
    {
    	$query = $this->db->query("SELECT * FROM tbl_exercises e
		JOIN
		tbl_exerciselevels el
		ON
		e.exerciseLevel_id = el.exerciseLevel_id
		WHERE
		e.exerciseLevel_id = '$exerciseLevel_id'");
    	if($query->num_rows() > 0) {
          return $query->result();
        } else {
          return 0;
        }

    }
}
